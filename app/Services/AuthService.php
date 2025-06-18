<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function loginUser(array $data) {
        $user = User::query()
            ->where('email', $data['email'])
            ->first();
        
        abort_if(empty($user), 401, 'Invalid credentials');

        abort_unless(Hash::check($data['password'], $user->password), 401, 'Invalid credentials');

        Auth::guard('web')
            ->login($user);

        $authData = $this->getAuth();

        return $authData;
    }

    public function getAuth() {
        abort_unless(Auth::guard('web')->check(), 401, 'Unauthorized');

        $user = Auth::user()->load(['role']);

        return $user;
    }

    public function logoutUser() {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        
        return true;
    }
}
