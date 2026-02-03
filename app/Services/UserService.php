<?php

namespace App\Services;

use App\Enums\FileDirectoryEnum;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = new User;
            $user->full_name = $data['full_name'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->role_id = $data['role_id'] ?? 3;

            if (isset($data['image'])) {
                $manageService = new ManageFileService;
                $result = $manageService->saveFile($data['image'], FileDirectoryEnum::PROFILE_IMAGE->value, 'public');

                $user->image = $result['file_name'];
            }

            $user->save();

            // TEMPORARY: Commented out to isolate 500 Error (Vite Manifest Issue)
            // try {
            //     $mailer = new MailerService;
            //     $mailer->sendEmailVerification($user);
            // } catch (\Throwable $th) {
            //     Log::error('Email verification failed: ' . $th->getMessage());
            // }

            return $user;
        });
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
        if (! empty($data['role_id'])) {
            $user->where('role_id', $data['role_id']);
        }

        // Order by role_id first (Admin=1, Staff=2, Customer=3), then by newest
        $user->orderBy('role_id', 'asc')
             ->orderBy('created_at', 'desc');

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
        $result = $manageService->saveFile($data['image'], FileDirectoryEnum::PROFILE_IMAGE->value, 'public');

        $user->image = $result['file_name'];

        $saved = $user->save();

        if ($saved && $oldImage) {
            $manageService->removeFile(FileDirectoryEnum::PROFILE_IMAGE->value, $oldImage, 'public');
        }

        return $user;
    }

    public function changeCover(array $data)
    {
        $user = User::query()
            ->where('user_id', $data['user_id'])
            ->first();

        abort_if(empty($user), 404, 'User not found');

        $oldImage = $user->cover_image;
        $manageService = new ManageFileService;
        $result = $manageService->saveFile($data['image'], FileDirectoryEnum::COVER_IMAGE->value, 'public');

        $user->cover_image = $result['file_name'];

        $saved = $user->save();

        if ($saved && $oldImage) {
            $manageService->removeFile(FileDirectoryEnum::COVER_IMAGE->value, $oldImage, 'public');
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

    public function customerList(array $data = []) {
        $query = User::query()
            ->with(['address.barangay.municity.province.region.islandGroup'])
            ->where('role_id', 3);
        
        // Apply date range filter if provided
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$data['start_date'], $data['end_date']]);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
}
