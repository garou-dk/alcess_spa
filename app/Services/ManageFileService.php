<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Enums\RoleEnum;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Pawlox\VideoThumbnail\Facade\VideoThumbnail;
use Ramsey\Uuid\Uuid;

class ManageFileService
{
    public function saveFile(UploadedFile $file, string $directory, string $access = 'local')
    {
        $extension = $file->extension();
        $fileName = Uuid::uuid4()->toString().'.'.$extension;

        $filePath = Storage::disk($access)->putFileAs($directory, $file, $fileName);

        abort_unless($filePath, 500, 'Failed to save file');

        return [
            'file_path' => $filePath,
            'file_name' => $fileName,
        ];
    }

    public function fetchProfile(array $data)
    {
        $authService = new AuthService;
        $user = $authService->getAuth();

        $adminAccess = in_array($user->role->role_name, RoleEnum::storeUsers());

        $fullPath = FileDirectoryEnum::PROFILE_IMAGE->value.'/'.$data['image'];

        abort_unless(Storage::disk('local')->exists($fullPath), 404, 'File not found');

        if ($adminAccess) {
            return Storage::disk('local')->path($fullPath);
        }

        abort_unless($user['image'] == $data['image'], 401, 'Access denied!');

        return Storage::disk('local')->path($fullPath);
    }

    public function removeFile(string $directory, string $fileName, string $access = 'local')
    {
        $filePath = "{$directory}/{$fileName}";

        if (Storage::disk($access)->exists($filePath)) {
            return Storage::disk($access)->delete($filePath);
        }

        return false;
    }

    public function fileExtension(UploadedFile $file)
    {
        return $file->extension();
    }

    public function checkOrCreateFolderDirectory(string $path)
    {

        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    public function createVideoThumbnail(string $filePath)
    {
        $thumbnailDirectory = storage_path('app/public/'.FileDirectoryEnum::THUMBNAIL_IMAGE->value);
        $this->checkOrCreateFolderDirectory($thumbnailDirectory);
        $thumbnailName = Uuid::uuid4()->toString().'.png';

        // $ffmpeg = FFMpeg::create();
        // $video = $ffmpeg->open($filePath);
        // $video->frame(TimeCode::fromSeconds(10))
        //     ->save(storage_path('app/public/'.FileDirectoryEnum::THUMBNAIL_IMAGE->value). '/' . $thumbnailName);

        VideoThumbnail::createThumbnail(
            storage_path("app/public/{$filePath}"),
            storage_path('app/public/'.FileDirectoryEnum::THUMBNAIL_IMAGE->value),
            $thumbnailName,
            1,
            128,
            128
        );

        return $thumbnailName;
    }

    public function createImageThumbnail(string $filePath)
    {
        $thumbnailDirectory = storage_path('app/public/'.FileDirectoryEnum::THUMBNAIL_IMAGE->value);
        $this->checkOrCreateFolderDirectory($thumbnailDirectory);

        $thumbnailName = Uuid::uuid4()->toString().'.png';
        $manager = new ImageManager(new Driver);

        $image = $manager->read(storage_path("app/public/{$filePath}"));
        $image->scale(width: 128, height: 128);
        $image->toPng()->save(storage_path('app/public/'.FileDirectoryEnum::THUMBNAIL_IMAGE->value.'/'.$thumbnailName));

        return $thumbnailName;
    }
    
    public function getConfigurationImage(array $data) {
        $fullPath = FileDirectoryEnum::CONFIGURATION_IMAGE->value.'/'.$data['value'];
        
        abort_unless(Storage::disk('local')->exists($fullPath), 404, 'File not found');
        
        return Storage::disk('local')->path($fullPath);
    }
}
