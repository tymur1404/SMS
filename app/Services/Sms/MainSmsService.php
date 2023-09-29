<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class MainSmsService extends SmsServiceAbstract
{
    public function sendSMS(GateQueue $params, string $message): ?string
    {
        // Some code

        $response = $this->getResponse();
        return $this->formatResponseId($response);
    }

    public function getResponse(): array
    {
        // Some code

        return [
            'status' => '200',
            'message' => 'OK',
        ];
    }

    private function formatResponseId(array $response): string
    {
        return $response["status"] . "." . $response["message"];
    }

}
