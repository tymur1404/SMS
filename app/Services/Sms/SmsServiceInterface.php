<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

interface SmsServiceInterface
{
    public function sendSms(GateQueue $params, string $message): ?string;
}
