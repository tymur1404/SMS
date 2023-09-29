<?php

namespace App\Services\Sms\Soap;


use App\Models\GateQueue;
use App\Services\Sms\SmsServiceAbstract;
use SoapClient;

class SoftlineService extends SmsServiceAbstract implements SoapServiceInterface
{
    const PARAM = 'WSI.xml';

    private $client;

    public function __construct(SoapClient $soapClient)
    {
        $this->client = $soapClient;
    }
    public function sendSms(GateQueue $params, string $message): ?string
    {
        $params = $this->getPrepareParams($params, $message);

        $sms = $this->client->sendMessages($params);

        return $this->formatSmsResult($sms);
    }

    public function getPrepareParams(GateQueue $params, string $message): array
    {
        return [
            'alfaName' => $params->senderName,
            'contacts' => [
                'phone' => $params->phone,
                'prop' => '',
            ],
            'template' => $message,
            'user' => [
                'password' => $params->password,
                'userName' => $params->username,
            ],
        ];
    }

    public function formatSmsResult(SoapClient $sms): string
    {
        return 'accepted=' . $sms->return->accepted . "; ID=" . $sms->return->notificationID;
    }
}
