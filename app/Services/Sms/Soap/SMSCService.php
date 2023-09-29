<?php

namespace App\Services\Sms\Soap;


use App\Models\GateQueue;
use App\Services\Sms\SmsServiceAbstract;
use SoapClient;

class SMSCService extends SmsServiceAbstract implements SoapServiceInterface
{
    const PARAM = 'https://smsc.ru/sys/soap.php?wsdl';

    private $client;

    public function __construct(SoapClient $soapClient)
    {
        $this->client = $soapClient;
    }
    public function sendSms(GateQueue $params, string $message): ?string
    {
        $params = $this->getPrepareParams($params, $message);

        $sms = $this->sendSms($this->client, $params);

        return $this->formatSmsResult($sms);
    }

    public function getPrepareParams(GateQueue $params, string $message ): array
    {
        $prepareParams = [
            'login' => $params->login,
            'psw' => $params->password,
            'phones' => $params->phone,
            'mes' => $message,
            'id' => '',
            'time' => 0,
            'sender' => $params->senderName
        ];

        if (strlen($params->senderName) > 0) {
            $prepareParams['sender'] = $params->senderName;
        }

        return $prepareParams;
    }

    public function formatSmsResult(SoapClient $sms)
    {
        $id = 'error';

        if ($sms->sendresult->error) {
            $id = 'error № ' . $sms->sendresult->error;
        }

        if (isset($sms->sendresult->id) &&
            (int)$sms->sendresult->id > 0) {

            $id = $sms->sendresult->id;
        }

        return $id;
    }
}
