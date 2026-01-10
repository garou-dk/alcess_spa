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

        // Verify current password
        abort_unless(
            Hash::check($data['current_password'], $user->password),
            401,
            'Current password is incorrect.'
        );

        // Validate new password
        abort_if(
            $data['new_password'] !== $data['new_password_confirmation'],
            422,
            'New passwords do not match.'
        );

        abort_if(
            strlen($data['new_password']) < 8,
            422,
            'New password must be at least 8 characters.'
        );

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
            'new_password' => Hash::make($data['new_password']),
            'tries' => $tries,
            'verification_attempts' => 0,
            'expiration' => $expiration,
        ], $expiration);

        // Send email with code
        $formattedDate = Carbon::parse($expiration)->format('F j, Y \a\t h:i A');
        Mail::queue(new SendPasswordResetMail($user->email, $code, $formattedDate));

        return true;
    }

    public function resetPassword(array $data)
    {
        // Find user by email
        $user = User::where('email', $data['email'])->first();
        abort_if(empty($user), 404, 'User not found.');

        $cacheKey = "password-reset-{$user->user_id}";
        
        abort_unless(Cache::has($cacheKey), 404, 'Reset code expired or not found.');

        $resetData = Cache::get($cacheKey);

        // Initialize verification attempts if not set
        if (!isset($resetData['verification_attempts'])) {
            $resetData['verification_attempts'] = 0;
        }

        // Check if maximum verification attempts exceeded (3 attempts)
        if ($resetData['verification_attempts'] >= 3) {
            Cache::forget($cacheKey);
            abort(429, 'Maximum verification attempts exceeded. Please request a new code.');
        }

        // Verify code
        if ($resetData['code'] !== $data['code']) {
            // Increment verification attempts
            $resetData['verification_attempts']++;
            $remainingAttempts = 3 - $resetData['verification_attempts'];
            
            // Update cache with new attempt count
            Cache::put($cacheKey, $resetData, Carbon::parse($resetData['expiration']));
            
            if ($remainingAttempts > 0) {
                abort(401, "Invalid verification code. You have {$remainingAttempts} attempt(s) remaining.");
            } else {
                Cache::forget($cacheKey);
                abort(429, 'Maximum verification attempts exceeded. Please request a new code.');
            }
        }

        // Update password
        $user->update([
            'password' => $resetData['new_password'],
        ]);

        // Clear cache
        Cache::forget($cacheKey);

        return true;
    }
}
