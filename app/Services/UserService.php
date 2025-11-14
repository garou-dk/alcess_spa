<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserService
{
    public function createUser(array $data): User
    {
        $user = new User;
        $user->full_name = $data['full_name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->role_id = $data['role_id'] ?? 3;

        if (isset($data['image'])) {
            $manageService = new ManageFileService;
            $result = $manageService->saveFile($data['image'], FileDirectoryEnum::PROFILE_IMAGE->value);

            $user->image = $result['file_name'];
        }

        $user->save();

        $mailer = new MailerService;
        $mailer->sendEmailVerification($user);

        return $user;
    }

    public function verifyEmail(array $data)
    {
        if (Cache::has("user-send-verification-{$data['uuid']}")) {
            $info = Cache::get("user-send-verification-{$data['uuid']}");

            $user = User::query()
                ->where('user_id', $info['user_id'])
                ->first();

            $user->markEmailAsVerified();

            Cache::forget("user-send-verification-{$data['uuid']}");

            Cache::forget("user-info-verification-{$user->user_id}");

            return true;
        }

        return false;
    }

    public function fetchUserPaginate(array $data)
    {
        $user = User::query();
        $user->with(['role']);

        if (! empty($data['search']) && ! blank($data['search'])) {
            $user->whereLike('full_name', "%{$data['search']}%");
        }
        if (isset($data['status'])) {
            $user->where('is_active', $data['status']);
        }
        $user->where('role_id', $data['role_id']);

        return $user->paginate($data['limit'] ?? 5);
    }

    public function changeName(array $data)
    {
        $user = User::query()
            ->where('user_id', $data['user_id'])
            ->first();

        abort_if(empty($user), 404, 'User not found');

        $user->full_name = $data['full_name'];
        $user->save();

        return $user;
    }

    public function changePassword(array $data)
    {
        $user = User::query()
            ->where('user_id', $data['user_id'])
            ->first();

        abort_if(empty($user), 404, 'User not found');

        $user->password = $data['password'];
        $user->save();

        return $user;
    }

    public function changeProfile(array $data)
    {
        $user = User::query()
            ->where('user_id', $data['user_id'])
            ->first();

        abort_if(empty($user), 404, 'User not found');

        $oldImage = $user->image;
        $manageService = new ManageFileService;
        $result = $manageService->saveFile($data['image'], FileDirectoryEnum::PROFILE_IMAGE->value);

        $user->image = $result['file_name'];

        $saved = $user->save();

        if ($saved && $oldImage) {
            $manageService->removeFile(FileDirectoryEnum::PROFILE_IMAGE->value, $oldImage);
        }

        return $user;
    }

    public function changeRole(array $data)
    {
        $user = User::query()
            ->where('user_id', $data['user_id'])
            ->first();

        abort_if(empty($user), 404, 'User not found');

        $user->role_id = $data['role_id'];
        $user->save();

        return $user;
    }

    public function changeStatus(array $data)
    {
        $user = User::query()
            ->where('user_id', $data['user_id'])
            ->first();

        abort_if(empty($user), 404, 'User not found');

        $user->is_active = $data['is_active'];
        $user->save();

        return $user;
    }

    public function customerList() {
        return User::query()
            ->with(['address.barangay.municity.province.region.islandGroup'])
            ->where('role_id', 3)
            ->get();
    }
}
