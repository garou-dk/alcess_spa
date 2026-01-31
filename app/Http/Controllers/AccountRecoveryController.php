<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountRecoveryController extends Controller
{
    public function __construct(protected SecurityService $service) {}

    /**
     * Recover account using a backup code.
     */
    public function recoverByCode(Request $request)
    {
        $request->validate([
            'recovery_code' => ['required', 'string', 'size:10'],
        ]);

        $user = $this->service->verifyAndUseRecoveryCode($request->recovery_code);

        if (!$user) {
            return ApiResponse::not_found()
                ->message('Invalid or expired recovery code.')
                ->response();
        }

        // Return email so user remembers what it was
        return ApiResponse::success()
            ->data(['email' => $user->email])
            ->message('Recovery code verified. Your email is ' . $user->email . '. Please reset your password now.')
            ->response();
    }

    /**
     * Get security question for an identifier (Email or Name).
     */
    public function getSecurityQuestion(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        $user = \App\Models\User::where('email', $request->identifier)
            ->orWhere('full_name', $request->identifier)
            ->first();

        if (!$user || !$user->security_question) {
            return ApiResponse::not_found()
                ->message('Security question not set for this account.')
                ->response();
        }

        return ApiResponse::success()
            ->data(['security_question' => $user->security_question])
            ->response();
    }

    /**
     * Verify security answer.
     */
    public function verifySecurityAnswer(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'security_answer' => ['required', 'string'],
        ]);

        $user = $this->service->verifySecurityAnswer($request->identifier, $request->security_answer);

        if (!$user) {
            return ApiResponse::not_found()
                ->message('Invalid security answer.')
                ->response();
        }

        return ApiResponse::success()
            ->data(['email' => $user->email])
            ->message('Answer verified. Your email is ' . $user->email . '. Please reset your password now.')
            ->response();
    }

    /**
     * Update security settings (for authenticated users).
     */
    public function updateSecuritySettings(Request $request)
    {
        $user = $request->user();
        
        $data = $request->validate([
            'security_question' => ['nullable', 'string', 'max:255'],
            'security_answer' => ['nullable', 'string', 'max:255'],
            'generate_codes' => ['nullable', 'boolean'],
        ]);

        $result = [];

        if (!empty($data['security_question']) && !empty($data['security_answer'])) {
            $this->service->updateSecurityQuestion($user, $data);
            $result['status'] = 'Security question updated.';
        }

        if (!empty($data['generate_codes'])) {
            $result['recovery_codes'] = $this->service->generateRecoveryCodes($user);
        }

        return ApiResponse::success()
            ->data($result)
            ->message('Security settings updated successfully.')
            ->response();
    }
}
