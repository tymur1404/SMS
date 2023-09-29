<?php

namespace App\Services\Sms;


use App\Models\GateQueue;
use App\Repositories\PostRouteRepository;
use App\Repositories\PsPymessRepository;
use Illuminate\Database\Eloquent\Model;

class OkitokiService extends SmsServiceAbstract
{
    const LIRA_SMS = 'true_1';
    const GSM_SMS = 'true';
    public function sendSms(GateQueue $params, string $message): ?string
    {
        $postRoute = PostRouteRepository::getPostRoute($params->id);

        if ($this->isSessionValid($postRoute)) {
            PsPymessRepository::sendLiraSms($postRoute->sessionId);
            return self::LIRA_SMS;
        }

        if ($postRoute->pid == 1) {
            if (self::strSplitUnicode($message) > 70)
            {
                PsPymessRepository::sendLongGsmSms($postRoute, $message);
            }
            else
            {
                PsPymessRepository::sendShortGsmSms($postRoute, $message);
            }
            return self::GSM_SMS;
        }

        return '';
    }

    private function isSessionValid(Model $postRoute): bool
    {
        return $postRoute->session_id > 0 && $postRoute->pid > 1;
    }
}
