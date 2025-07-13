<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Enums\RoleEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
}
