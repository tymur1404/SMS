<?php

namespace App\Repositories;

use App\Models\GateLinkToCompany;
use App\Models\GateQueue;

class GateLinkToCompanyRepository
{
    public static function getPriceFromGateLinkToCompany(GateQueue $gateQueue, int $gateId ): int
    {
        $price =  GateLinkToCompany::where('company', $gateQueue->schema_name)
            ->where('gate_id', $gateId)
            ->value('price');

        return $price ?? 0;
    }
}
