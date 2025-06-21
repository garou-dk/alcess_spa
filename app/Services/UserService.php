<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserService
{
    public function createUser(array $data)
    {
        $user = User::query()
            ->create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role_id' => $data['role_id'] ?? 3,
            ]);

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
}
