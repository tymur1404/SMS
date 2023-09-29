<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class Sms100Service extends SmsServiceAbstract
{
    public function sendSms(GateQueue $params, string $message): ?string
    {

        $res = null;//some code

        if (!is_null($res)) {
            if ((int)$res[0] === 0) {
                return "success";
            }

            return "error " . $res[0];
        }

        return 'error';
    }

}
