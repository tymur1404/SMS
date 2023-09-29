<?php

namespace App\Services\Sms\Protech;

use App\Models\GateQueue;

class Protech4Service extends ProtechServiceAbstract
{

    private const SCRIPT_FILE = 'smsSendNow.cgi';
    private const PROTOCOL_HTTP = "http";


    public function sendSms(GateQueue $params, string $message): ?string
    {
        $this->sendSlaveRequest($params);
        sleep(1);
        $post = $this->getPrepareData($params, $message);
        return $this->sendPostRequest($params->ip, $post, $params->port);
    }

}
