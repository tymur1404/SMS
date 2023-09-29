<?php

namespace App\Repositories;

use App\Models\GateQueue;

class GateQueueRepository
{

    public static function getGateQueue(int $gateId): GateQueue
    {
        return GateQueue::where('gate_id', $gateId)
            ->whereNull('sms_text')
            ->findOrFail();
    }
    public static function updateGateQueue(int $id, ?string $key): void
    {
        $queue = GateQueue::find($id);

        if (!$queue) {
            return ;
        }

        if ($key === 'true_1') {
            $queue->notified = 1;
        } else {
            $queue->delete();
        }

        $queue->save();
    }
}
