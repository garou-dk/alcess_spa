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

    public function verifyRecoveryKey(\Illuminate\Http\Request $request, PasswordResetService $service): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'recovery_key' => ['required', 'string'],
        ]);

        $service->verifyRecoveryKey($data);

        return response()->json([
            'message' => 'Recovery key verified successfully.',
        ]);
    }

    public function getSecurityQuestion(\Illuminate\Http\Request $request, PasswordResetService $service): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        return response()->json([
            'question' => $service->getSecurityQuestionForEmail($data['email']),
        ]);
    }

    public function verifySecurityAnswer(\Illuminate\Http\Request $request, PasswordResetService $service): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'answer' => ['required', 'string'],
        ]);

        $service->verifySecurityAnswer($data);

        return response()->json([
            'message' => 'Security answer verified successfully.',
        ]);
    }
}
