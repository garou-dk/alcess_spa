<?php

namespace App\Http\Controllers;

use App\Enums\AppConfigurationEnum;
use App\Enums\FileDirectoryEnum;
use App\Helpers\ApiResponse;
use App\Http\Requests\SetBankAccountRequest;
use App\Services\AppConfigurationService;
use App\Services\ManageFileService;
use Illuminate\Http\Request;

class AppConfigurationController extends Controller
{
    public function __construct(public AppConfigurationService $service) {}

    public function getOnlineBank(SetBankAccountRequest $request) {
        $screenshot = $this->service->getConfiguration([
            'key' => AppConfigurationEnum::OnlineBankScreenShot->value
        ]);
        $bankAccount = $this->service->getConfiguration([
            'key' => AppConfigurationEnum::OnlineBankAccount->value
        ]);

        return ApiResponse::success()
            ->data([
                'screenshot' => $screenshot,
                'bank_account' => $bankAccount
            ])
            ->response();
    }

    public function setBankAccount(SetBankAccountRequest $request) {
        $data = $request->validated();
        $fileService = new ManageFileService();

        $screenshot = $fileService->saveFile($data['screenshot'], FileDirectoryEnum::CONFIGURATION_IMAGE->value);

        $ss = $this->service->updateConfiguration([
            'key' => AppConfigurationEnum::OnlineBankScreenShot->value,
            'value' => $screenshot['file_name']
        ]);

        $account = $this->service->updateConfiguration([
            'key' => AppConfigurationEnum::OnlineBankAccount->value,
            'value' => $data['account_number']
        ]);

        return ApiResponse::success()
            ->data([
                'screenshot' => $ss,
                'bank_account' => $account
            ])
            ->message('Bank account saved successfully.')
            ->response();
    }

    public function getBankAccountScreenshot() {
        $screenshot = $this->service->getConfiguration([
            'key' => AppConfigurationEnum::OnlineBankScreenShot->value
        ]);

        abort_if(empty($screenshot), 404, 'Screenshot not found.');
        abort_if($screenshot->value == null, 404, 'Screenshot not found.');

        $fileService = new ManageFileService();

        $image = $fileService->getConfigurationImage([
            'value' => $screenshot->value
        ]);

        return ApiResponse::success()
            ->showFile($image);
    }
}
