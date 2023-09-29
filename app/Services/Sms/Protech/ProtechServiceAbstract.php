<?php

namespace App\Services\Sms\Protech;

use App\Models\GateQueue;
use App\Services\Sms\SmsServiceAbstract;
use App\Services\Sms\SmsServiceInterface;

class ProtechServiceAbstract extends SmsServiceAbstract
{
    private const SCRIPT_FILE = "smsSendNow.cgi";
    private const SEND_MESSAGE = "Send Now";
    private const DEFAULT_PORT = 80;
    private const PROTOCOL_HTTP = "http";
    private const PROTOCOL_HTTPS = "https";

    private const SLAVE_0 = 0;
    private const SLAVE_1 = 1;
    private const SLAVE_2 = 2;
    private const B_MOBILE_0 = 0;
    private const B_MOBILE_1 = 1;
    private const LINE_1 = 1;
    private const LINE_3 = 3;

    public function sendSms(GateQueue $params, string $message): ?string
    {
        $post = $this->getPrepareData($params->phone, $message, $params->encode);
        $url = $this->buildUrl($params->ip, $params->port);

        $this->sendPostRequest($url, $post);
    }

    public function getPrepareData(GateQueue $params, string $message): array
    {
        return [
            'Encode' => $params->encode,
            'bMobile' => $this->getBMobileValue($params->line),
            'DEST' => $params->phone,
            'HEXTEXT' => $message,
            'Send' => self::SEND_MESSAGE,
        ];
    }


    protected function getBMobileValue(int $line): int
    {
        return ($line === self::LINE_1 || $line === self::LINE_3) ? self::B_MOBILE_0 : self::B_MOBILE_1;
    }

    private function buildUrl(string $ip, int $port): string
    {
        return sprintf("%s://%s:%s/%s" , self::PROTOCOL_HTTP, $ip, $port, self::SCRIPT_FILE);
    }

    protected function sendSlaveRequest(GateQueue $params): void
    {
        $post = ['SlaveNum' => $params->line > self::SLAVE_2 ? self::SLAVE_1 : self::SLAVE_0];
        $this->sendPostRequest($params->ip, $post, $params->port);
    }
}
