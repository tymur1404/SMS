<?php

namespace App\Services\Sms;

use App\Models\GateQueue;
use JsonException;

class EsputnikService extends SmsServiceAbstract
{
    const URL = 'https://esputnik.com/api/v1/message/sms';

    /**
     * @throws JsonException
     */
    public function sendSms(GateQueue $params, string $message): ?string
    {
        $requestData = $this->buildRequestData($params->sender_name, $params->text, $params->phone);
        $response = $this->sendRequest($params, $requestData);
        $response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return $this->getStatus($response);
    }
    private function buildRequestData(string $sender_name, string $text, string $phone): array
    {
        return [
            'text' => $text,
            'from' => $sender_name,
            'phoneNumbers' => $phone,
        ];
    }

    private function getStatus(array $response): string|null
    {
        return $json['results']['status'] ?? null;
    }

    /**
     * @throws JsonException
     */
    private function sendRequest(GateQueue $params, array $requestData): bool|string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData, JSON_THROW_ON_ERROR));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, self::URL);
        curl_setopt($ch, CURLOPT_USERPWD, $params->user . ':' . $params->password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

}

