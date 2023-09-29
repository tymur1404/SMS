<?php

namespace App\Repositories;

use App\Models\Gate;

class GateRepository
{
    public static function getGate(int $gateId): Gate
    {
        return Gate::join('gate_types', 'gates.gate_type', '=', 'gate_types.id')
            ->select('gates.*', 'gate_types.type_abbr')
            ->where('gates.id', $gateId)
            ->where('gates.enabled', 1)
            ->where('gates.has_sms', '>', 0);
    }
}
