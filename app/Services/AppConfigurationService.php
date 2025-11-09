<?php

namespace App\Services;

use App\Models\AppConfiguration;

class AppConfigurationService
{
    public function getConfiguration(array $data) {
        $result = AppConfiguration::where('key', $data['key'])
            ->first();

        abort_if(empty($result), 404, 'Configuration not found.');

        return $result;
    }

    public function updateConfiguration(array $data) {
        $result = AppConfiguration::where('key', $data['key'])
            ->first();

        abort_if(empty($result), 404, 'Configuration not found.');

        $result->value = $data['value'];
        $result->save();

        return $result;
    }
}
