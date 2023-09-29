<?php

namespace App\Services\Sms\Protech;

use App\Models\GateQueue;


class Protech1Service extends ProtechServiceAbstract
{
    private const SCRIPT_FILE = "smsSendNow.cgi";
    private const SEND_MESSAGE = "Send Now";
    private const DEFAULT_PORT = 80;
    private const PROTOCOL_HTTP = "http";
    private const PROTOCOL_HTTPS = "https";

    public function sendSms(GateQueue $params, string $message): ?string
    {
        $post = $this->getPrepareData($params->phone, $message, $params->encode);
        $url = $this->buildUrl($params->ip, $params->port);

        return $this->sendPostRequest($url, $post);
    }

    public function getPrepareData(GateQueue $params, string $message): array
    {
        return [
            "Encode" => $params->encode,
            "DEST" => $params->phone,
            "HEXTEXT" => $message,
            "Send" => self::SEND_MESSAGE,
        ];
    }

    private function buildUrl(string $ip, int $port): string
    {
        $protocol = $this->getProtocol($port);

        return sprintf("%s://%s:%s/%s" , $protocol, $ip, $port, self::SCRIPT_FILE);
    }

    private function getProtocol(int $port): string
    {
        return $port > self::DEFAULT_PORT ? self::PROTOCOL_HTTPS : self::PROTOCOL_HTTP;
    }

}
