<?php

use App\Enums\RoleEnum;
use App\Services\AuthService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('product', function (User $user) {
    $authService = new AuthService;
    $user = $authService->getAuth();

    return in_array($user->role->role_name, RoleEnum::storeUsers());
});
