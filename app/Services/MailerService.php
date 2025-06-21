<?php

namespace App\Services;

use App\Mail\SendVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Ramsey\Uuid\Uuid;

class MailerService
{
    public function sendEmailVerification(User $user) {
        $uuid = Uuid::uuid4()->toString();

        $expiration = now()->addDay();

        Cache::put("user-info-verification-{$user->user_id}", [
            'user_id' => $user->user_id,
            'tries' => 1,
            'uuid' => $uuid
        ], $expiration);

        Cache::put("user-send-verification-{$uuid}", [
            'user_id' => $user->user_id,
        ], $expiration);

        $url = URL::temporarySignedRoute(
            'verify-email',
            $expiration,
            [
                'uuid' => $uuid
            ]
        );

        $link = url('verify') . '?' . http_build_query([
            'link' => urlencode($url)
        ]);

        $formattedDate = $expiration->format('F j, Y, \a\t h:i A');
        $mail = new SendVerificationMail($user->email, $link, $formattedDate);
        Mail::send($mail);
    }
}
