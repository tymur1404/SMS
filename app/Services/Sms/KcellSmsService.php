<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class KcellSmsService extends SmsServiceAbstract
{
    private const URL = 'https://api.kcell.kz/app/smsgw/rest/v2/messages';
    private const TIME_BOUNDS = 'ad99';

    /**
     * @throws \JsonException
     */
    public function sendSMS(GateQueue $params, string $message): ?string
    {
        $data = [
            "client_message_id" => time(),
            "sender" => $params->sender_name,
            "recipient" => $params->phone,
            "message_text" => $params->text,
            "time_bounds" => self::TIME_BOUNDS,
        ];

        return $this->sendRequest($params, $data);
    }

    /**
     * @throws \JsonException
     */
    private function sendRequest(GateQueue $params, array $data): bool|string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json;charset=utf-8']);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $params->login . ":" . $params->password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
