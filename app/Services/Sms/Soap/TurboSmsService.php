<?php

namespace App\Services\Sms\Soap;


use App\Models\GateQueue;
use App\Services\Sms\SmsServiceAbstract;
use Illuminate\Support\Collection;
use SoapClient;

class TurboSmsService extends SmsServiceAbstract implements SoapServiceInterface
{
    const PARAM = 'http://turbosms.in.ua/api/wsdl.html';

    private $client;

    public function __construct(SoapClient $soapClient)
    {
        $this->client = $soapClient;
    }
    public function sendSms(GateQueue $params, string $message): ?string
    {

        $authResult = $this->authenticate( $params->login, $params->pass);

        if ($this->isAuthenticationSuccessful($authResult)) {
            $creditBalance = $this->client->GetCreditBalance();

            if ($this->hasSufficientCredits($creditBalance)) {
                $sms = $this->getPrepareParams($params, $message);

                $sendResult = $this->client->sendMessages($sms);

                return $this->formatSmsResult($sendResult);
            }

            return "Исчерпан лимит смс";
        }

        return $authResult;
    }

    private function authenticate(string $login, string $pass): Collection
    {
        $auth = [
            'login' => $login,
            'password' => $pass
        ];

        return $this->client->Auth($auth);
    }

    private function isAuthenticationSuccessful(Collection $authResult): bool
    {
        return strstr($authResult->AuthResult, 'успешно');
    }

    private function hasSufficientCredits($creditBalance): bool
    {
        return (int)$creditBalance->GetCreditBalanceResult > 0;
    }

    public function formatSmsResult(SoapClient $sms): string
    {
        if (is_array($sms->SendSMSResult->ResultArray)) {
            return implode('|', $sms->SendSMSResult->ResultArray);
        }

        return $sms->SendSMSResult->ResultArray;
    }

    function getPrepareParams(GateQueue $params, string $message)
    {
        return [
            'sender' => $params->sender_name,
            'destination' => '+' . trim($params->phone),
            'text' => $params->text
        ];
    }
}
