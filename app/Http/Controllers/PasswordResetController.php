<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\SendPasswordResetCodeRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function sendCode(SendPasswordResetCodeRequest $request, PasswordResetService $service): JsonResponse
    {
        $service->sendResetCode($request->validated());

        return response()->json([
            'message' => 'Verification code sent to your email.',
        ]);
    }

    public function reset(PasswordResetRequest $request, PasswordResetService $service): JsonResponse
    {
        $service->resetPassword($request->validated());

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }

    public function verify(\Illuminate\Http\Request $request, PasswordResetService $service): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $service->verifyCode($data);

        return response()->json([
            'message' => 'Code verified successfully.',
        ]);
    }
}
