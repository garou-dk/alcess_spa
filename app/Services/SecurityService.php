<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SecurityService
{
    /**
     * Generate 8 new recovery codes for a user.
     */
    public function generateRecoveryCodes(User $user): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(10));
        }

        $user->update([
            'recovery_codes' => $codes, // Model handles hashing if we want, but usually codes are shown once
        ]);

        return $codes;
    }

    /**
     * Update security question and answer.
     */
    public function updateSecurityQuestion(User $user, array $data)
    {
        $user->update([
            'security_question' => $data['security_question'],
            'security_answer' => $data['security_answer'], // Cast as hashed in model
        ]);

        return true;
    }

    /**
     * Verify a recovery code and use it (remove it from the list).
     */
    public function verifyAndUseRecoveryCode(string $code): ?User
    {
        // This is tricky if we don't have an email. We might need some identifier.
        // Let's assume for now they provide Full Name or something.
        // Or better, we ask for their email first. If they forgot email, they are really in trouble.
        // But usually, backup codes are for when you HAVE the email but NO 2FA/Password.
        
        // Let's stick to the user's request: "completely forgot its email and password".
        // In that case, we need to search ALL users for this recovery code.
        
        $users = User::whereNotNull('recovery_codes')->get();
        
        foreach ($users as $user) {
            $codes = $user->recovery_codes;
            if (in_array($code, $codes)) {
                // Remove the used code
                $newCodes = array_values(array_diff($codes, [$code]));
                $user->update(['recovery_codes' => $newCodes]);
                return $user;
            }
        }

        return null;
    }

    /**
     * Recover account via security question. Requires Email or Full Name as identifier.
     */
    public function verifySecurityAnswer(string $identifier, string $answer): ?User
    {
        $user = User::where('email', $identifier)
            ->orWhere('full_name', $identifier)
            ->first();

        if ($user && $user->security_answer && Hash::check($answer, $user->security_answer)) {
            return $user;
        }

        return null;
    }
}
