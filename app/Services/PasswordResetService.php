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

        // Set a verified flag in the cache so resetPassword knows the code was indeed verified recently
        $resetData['verified'] = true;
        Cache::put($cacheKey, $resetData, Carbon::parse($resetData['expiration']));

        return true;
    }

    public function verifyRecoveryKey(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        abort_if(empty($user), 404, 'User not found.');
        abort_unless($user->recovery_key, 400, 'No recovery key set for this account.');

        if ($user->recovery_key !== strtoupper($data['recovery_key'])) {
            abort(401, 'Invalid recovery key.');
        }

        // Create a temporary session in cache to allow password reset
        $expiration = now()->addMinutes(15);
        $cacheKey = "password-reset-{$user->user_id}";
        Cache::put($cacheKey, [
            'user_id' => $user->user_id,
            'verified' => true,
            'expiration' => $expiration,
            'method' => 'recovery_key'
        ], $expiration);

        return true;
    }

    public function getSecurityQuestionForEmail(string $email)
    {
        $user = User::where('email', $email)->first();
        abort_if(empty($user), 404, 'User not found.');
        abort_unless($user->security_question, 400, 'No security question set for this account.');

        return $user->security_question;
    }

    public function verifySecurityAnswer(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        abort_if(empty($user), 404, 'User not found.');
        abort_unless($user->security_answer, 400, 'No security question set for this account.');

        if (!Hash::check(strtolower(trim($data['answer'])), $user->security_answer)) {
            abort(401, 'Incorrect answer to security question.');
        }

        // Create a temporary session in cache to allow password reset
        $expiration = now()->addMinutes(15);
        $cacheKey = "password-reset-{$user->user_id}";
        Cache::put($cacheKey, [
            'user_id' => $user->user_id,
            'verified' => true,
            'expiration' => $expiration,
            'method' => 'security_question'
        ], $expiration);

        return true;
    }

    public function resetPassword(array $data)
    {
        // Find user by email
        $user = User::where('email', $data['email'])->first();
        abort_if(empty($user), 404, 'User not found.');

        $cacheKey = "password-reset-{$user->user_id}";
        
        abort_unless(Cache::has($cacheKey), 404, 'Reset session expired or not found.');

        $resetData = Cache::get($cacheKey);

        // Verify that the session was actually verified
        if (!isset($resetData['verified']) || !$resetData['verified']) {
            abort(401, "Session not verified.");
        }

        // Update password
        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        // Clear cache
        Cache::forget($cacheKey);

        return true;
    }
}
