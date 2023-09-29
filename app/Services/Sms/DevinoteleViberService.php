<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class DevinoteleViberService extends SmsServiceAbstract
{
    const URL = 'https://viber.devinotele.com:444/send';
    public function sendSMS(GateQueue $params, string $message): ?string
    {
        $messageData = $this->buildViberMessageData($params->sender_name, $params->text, $params->phone);
        $response = $this->sendViberRequest($params, $messageData);

        $response = json_decode($response, true);

        return $response['status'];
    }


    private function buildViberMessageData(string $senderName, string $text, string $phone): array
    {
        return [
            "resendSms" => true,
            "messages" => [
                [
                    "type" => "viber",
                    "subject" => $senderName,
                    "priority" => "high",
                    "validityPeriodSec" => 900,
                    "comment" => "comment",
                    "contentType" => "text",
                    "content" => [
                        "text" => $text
                    ],
                    "address" => $phone,
                    "smsText" => $text,
                    "smsSrcAddress" => $senderName,
                    "smsValidityPeriodSec" => 3600
                ]
            ]
        ];
    }

    private function sendViberRequest(GateQueue $params, array $messageData): bool|string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $params->login . ":" . $params->password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData, JSON_THROW_ON_ERROR));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($messageData, JSON_THROW_ON_ERROR))
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}
