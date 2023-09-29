<?php

namespace App\Services\Sms\Soap;

use App\Models\GateQueue;
use SoapClient;

interface SoapServiceInterface
{
    public function formatSmsResult(SoapClient $sms);

    public function getPrepareParams(GateQueue $params, string $message);
}
