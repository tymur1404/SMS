<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class StreamTelecomSmsService extends SmsServiceAbstract
{
    const URL = 'http://gateway.api.sc/get/';
    public function sendSms(GateQueue $params, string $message): ?string
    {
        $url = $this->buildRequestUrl($params);
        return $this->getRequest($url);
    }

    private function buildRequestUrl(GateQueue $params): string
    {
        $url = self::URL;
        $url .= "?user=" . $params->login;
        $url .= "&pwd=" . $params->password;
        $url .= "&sadr=" . $params->sender_name;
        $url .= "&dadr=" . $params->phone;
        $url .= "&text=" . urlencode($params->text);

        return $url;
    }

    private function getRequest($url): bool|string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }
}
