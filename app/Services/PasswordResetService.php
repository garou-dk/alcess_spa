<?php

namespace App\Services;

use App\Mail\SendPasswordResetMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetService
{
    public function sendResetCode(array $data)
    {
        // Find user by email
        $user = User::where('email', $data['email'])->first();
        abort_if(empty($user), 404, 'User not found.');

        // Verify current password if provided
        if (!empty($data['current_password'])) {
            abort_unless(
                Hash::check($data['current_password'], $user->password),
                401,
                'Current password is incorrect.'
            );
        }

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiration = now()->addMinutes(15);

        $cacheKey = "password-reset-{$user->user_id}";
        $tries = 1;

        if (Cache::has($cacheKey)) {
            $info = Cache::get($cacheKey);
            $info['tries']++;
            $tries = $info['tries'];

            $date = Carbon::parse($info['expiration'])->format('F j, Y \a\t h:i A');
            abort_if(
                $tries > 5,
                429,
                "You have exceeded the maximum number of attempts. Please try again on {$date}."
            );
        }

        // Store reset data in cache
        Cache::put($cacheKey, [
            'user_id' => $user->user_id,
            'code' => $code,
            'tries' => $tries,
            'verification_attempts' => 0,
            'expiration' => $expiration,
            'current_password_verified' => !empty($data['current_password']),
        ], $expiration);

        // Send email with code
        $formattedDate = Carbon::parse($expiration)->format('F j, Y \a\t h:i A');
        Mail::queue(new SendPasswordResetMail($user->email, $code, $formattedDate));

        return true;
    }

    public function verifyCode(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        abort_if(empty($user), 404, 'User not found.');

        $cacheKey = "password-reset-{$user->user_id}";
        abort_unless(Cache::has($cacheKey), 404, 'Reset code expired or not found.');

        $resetData = Cache::get($cacheKey);

        if ($resetData['verification_attempts'] >= 3) {
            Cache::forget($cacheKey);
            abort(429, 'Maximum verification attempts exceeded. Please request a new code.');
        }

        if ($resetData['code'] !== $data['code']) {
            $resetData['verification_attempts']++;
            $remainingAttempts = 3 - $resetData['verification_attempts'];
            Cache::put($cacheKey, $resetData, Carbon::parse($resetData['expiration']));

            if ($remainingAttempts > 0) {
                abort(401, "Invalid verification code. You have {$remainingAttempts} attempt(s) remaining.");
            } else {
                Cache::forget($cacheKey);
                abort(429, 'Maximum verification attempts exceeded. Please request a new code.');
            }
        }

        return true;
    }

    public function resetPassword(array $data)
    {
        // Find user by email
        $user = User::where('email', $data['email'])->first();
        abort_if(empty($user), 404, 'User not found.');

        if (!empty($data['code'])) {
            $cacheKey = "password-reset-{$user->user_id}";
            abort_unless(Cache::has($cacheKey), 404, 'Reset session expired or not found.');
            $resetData = Cache::get($cacheKey);
            if ($resetData['code'] !== $data['code']) {
                abort(401, "Invalid verification code.");
            }
            Cache::forget($cacheKey);
        } elseif (!empty($data['recovery_code'])) {
            $securityService = app(\App\Services\SecurityService::class);
            $verifiedUser = $securityService->verifyAndUseRecoveryCode($data['recovery_code']);
            abort_unless($verifiedUser && $verifiedUser->user_id === $user->user_id, 401, 'Invalid recovery code.');
        } elseif (!empty($data['security_answer'])) {
            $securityService = app(\App\Services\SecurityService::class);
            $verifiedUser = $securityService->verifySecurityAnswer($user->email, $data['security_answer']);
            abort_unless($verifiedUser && $verifiedUser->user_id === $user->user_id, 401, 'Invalid security answer.');
        } else {
            abort(400, 'Verification proof required.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        // Clear cache
        Cache::forget($cacheKey);

        return true;
    }
}
