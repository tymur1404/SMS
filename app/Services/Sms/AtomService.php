<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class AtomService extends SmsServiceAbstract
{
    const URL = 'http://atompark.com/members/sms/xml.php';

    public function sendSms(GateQueue $params, string $message): ?string
    {
        $xml = $this->buildXml($params);

        return $this->sendRequest($xml);
    }

    private function buildXml(GateQueue $params): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
    <SMS>
        <operations>
            <operation>SEND</operation>
        </operations>
        <authentification>
            <username>' . $params->login . '</username>
            <password>' . $params->pass . '</password>
        </authentification>
        <message>
            <sender>' . $params->sender_name . '</sender>
            <text>' . $params->text . '</text>
        </message>
        <numbers>
            <number>' . $params->phone . '</number>
        </numbers>
    </SMS>';

        return $xml;
    }

    private function sendRequest(string $xml): bool|string
    {
        $curl = curl_init();

        $curlOptions = array(
            CURLOPT_URL => self::URL,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_POST => true,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 100,
            CURLOPT_POSTFIELDS => ['XML' => $xml],
        );

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new \RuntimeException('HTTP request failed');
        }

        curl_close($curl);

        return $response;
    }

}
