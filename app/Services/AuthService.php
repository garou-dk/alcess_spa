<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function loginUser(array $data)
    {
        $user = User::query()
            ->where([
                'email' => $data['email'],
            ])
            ->whereIn('role_id', $data['role_id'])
            ->first();

        abort_if(empty($user), 401, 'Invalid credentials');

        abort_unless(Hash::check($data['password'], $user->password), 401, 'Invalid credentials');

        if (! $user->hasVerifiedEmail()) {
            $mailer = new MailerService;
            $mailer->sendEmailVerification($user);
            abort(401, 'We sent you an email to verify your account. Please check your email inbox or spam folder.');
        }

        // Invalidate previous session if exists
        if ($user->current_session_id) {
            \DB::table('sessions')->where('id', $user->current_session_id)->delete();
        }

        Auth::guard('web')
            ->login($user);

        // Store the new session ID
        $sessionId = session()->getId();
        $user->update(['current_session_id' => $sessionId]);

        $authData = $this->getAuth();

        return $authData;
    }

    public function getAuth()
    {
        abort_unless(Auth::check(), 401, 'Unauthorized');

        $user = Auth::guard('web')->user()->load(['role']);

        return $user;
    }

    public function logoutUser()
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            // Clear the current session ID
            if ($user) {
                $user->update(['current_session_id' => null]);
            }
            
            Auth::guard('web')->logout();
            session()->flush();
            session()->regenerate();
        }

        return true;
    }
}
