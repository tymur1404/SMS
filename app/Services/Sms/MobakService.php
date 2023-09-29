<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class MobakService extends SmsServiceAbstract
{
    protected const SUCCESS = 'success';
    protected const ERROR = 'error';
    public function sendSMS(GateQueue $params, string $message): ?string
    {
        $result = $this->send([
            'message' => $params->text,
            'sender' => $params->sender_name,
            'phone' => $params->phone,
        ]);

        $responseArray = $result->asArray();

        if (isset($responseArray['information']['@attributes']['code']) &&
            (int)$responseArray['information']['@attributes']['code'] === 0) {

            return self::SUCCESS;
        }

        return self::ERROR;
    }

}
