<?php

namespace App\Repositories;

use App\Models\GateQueue;
use App\Models\SmsLog;

class SmsLogRepository
{
    public static function createSmsLog(GateQueue $queueGate,
                                   float $gateLinkPrice,
                                   float $gatePrice,
                                   string $key,
                                   string $status,
                                   int $gateId): void
    {
        SmsLog::create([
//            'column_name_1' => 'default', // значения по умолчанию в базе
//            'column_name_2' => 0,
//            'column_name_3' => 0,
//            'column_name_4' => 0,
            'sms_text' => strip_tags($queueGate->sms_text),
            'gate_id' => $gateId,
            'gate_link_price' => $gateLinkPrice,
            'gate_price' => $gatePrice,
            'phone' => strip_tags($queueGate->phone),
            'schema_name' => strip_tags($queueGate->schemaName),
            'crm_id' => $queueGate->crm_id,
            'key' => $key,
            'user_id' => $queueGate->use_id,
            'status' => $status
        ]);
    }
}
