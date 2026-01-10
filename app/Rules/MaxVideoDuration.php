<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class MaxVideoDuration implements ValidationRule
{
    protected int $maxDuration;

    public function __construct(int $maxDuration = 15)
    {
        $this->maxDuration = $maxDuration;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            return;
        }

        $mimeType = $value->getMimeType();
        
        // Only validate video files
        if (!str_starts_with($mimeType, 'video/')) {
            return;
        }

        // Check if getID3 library is available
        $getID3Path = base_path('vendor/james-heinrich/getid3/getid3/getid3.php');
        if (!file_exists($getID3Path)) {
            // If getID3 is not available, skip validation
            return;
        }

        try {
            require_once $getID3Path;
            $getID3 = new \getID3();
            $fileInfo = $getID3->analyze($value->getRealPath());
            
            if (isset($fileInfo['playtime_seconds'])) {
                $duration = $fileInfo['playtime_seconds'];
                
                if ($duration > $this->maxDuration) {
                    $fail("The {$attribute} video must not be longer than {$this->maxDuration} seconds.");
                }
            }
        } catch (\Exception $e) {
            // If we can't determine duration, allow the file
            return;
        }
    }
}
