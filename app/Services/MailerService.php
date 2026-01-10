<?php

namespace App\Services;

use App\Mail\SendVerificationMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Ramsey\Uuid\Uuid;

class MailerService
{
    public function sendEmailVerification(User $user)
    {
        $cacheKey = "user-info-verification-{$user->user_id}";
        $tries = 1;

        if (Cache::has($cacheKey)) {
            $info = Cache::get($cacheKey);
            $info['tries']++;
            $tries = $info['tries'];
            $uuid = $info['uuid'];
            $expiration = $info['expiration'];

            $date = Carbon::parse($expiration)->format('F j, Y \a\t h:i A');
            abort_if($tries > 10, 429, "You have exceeded the maximum number of attempts today. Please try again on {$date}.");
        } else {
            $uuid = Uuid::uuid4()->toString();
            $expiration = now()->addDay();
        }

        Cache::put($cacheKey, [
            'user_id' => $user->user_id,
            'tries' => $tries,
            'uuid' => $uuid,
            'expiration' => $expiration,
        ], $expiration);

        if (! Cache::has("user-send-verification-{$uuid}")) {
            Cache::put("user-send-verification-{$uuid}", [
                'user_id' => $user->user_id,
            ], $expiration);
        }

        $url = URL::temporarySignedRoute('verify-email', $expiration, [
            'uuid' => $uuid,
        ]);

        $link = url('verify').'?'.http_build_query([
            'link' => urlencode($url),
        ]);

        $formattedDate = Carbon::parse($expiration)->format('F j, Y \a\t h:i A');
        Mail::queue(new SendVerificationMail($user->email, $link, $formattedDate));
    }
}
