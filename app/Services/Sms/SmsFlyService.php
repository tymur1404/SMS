<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class SmsFlyService extends SmsServiceAbstract
{
    const URL = 'http://sms-fly.com/api/api.php';
    public function sendSMS(GateQueue $params, string $message): ?string
    {
        $xml = $this->buildXmlRequest( $params->sender_name, $params->text, $params->phone);
        return $this->makeApiRequest($params, $xml);
    }

    private function buildXmlRequest(string $sender_name, string $text, string $phone): string
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <request>
            <operation>SENDSMS</operation>
            <message start_time="AUTO" end_time="AUTO" rate="120" lifetime="1" desc="" source="' . $sender_name . '">
                <body>' . $text . '</body>
                <recipient>' . $phone . '</recipient>
            </message>
        </request>';

        return $xml;
    }

    private function makeApiRequest(GateQueue $params, string $xml)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERPWD, $this->login . ':' . $this->pass);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: text/xml", "Accept: text/xml"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}
