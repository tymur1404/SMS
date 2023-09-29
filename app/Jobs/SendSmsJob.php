<?php

namespace App\Jobs;

use App\Models\Gate;
use App\Models\GateQueue;
use App\Repositories\GateQueueRepository;
use App\Repositories\SmsLogRepository;
use App\Services\Sms\SmsServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(protected SmsServiceInterface $gateService,
                                protected GateQueue $queueItem,
                                protected float $gateLinkPrice,
                                protected Gate $gate,
                                protected string $message){}

    public function handle():string
    {
        $message = $this->gateService->sendSms($this->queueItem, $this->message);

        [$key, $status] = $this->parseKeyAndStatus($message);

        SmsLogRepository::createSmsLog($this->queueItem,
            $this->gateLinkPrice,
            $this->gate->price,
            $key,
            $status,
            $this->gate->id);

        GateQueueRepository::updateGateQueue($this->queueItem->id, $key);
    }

    private function parseKeyAndStatus(string $message): array
    {
        $result = [];

        if ($message !== '' && str_contains($message, "|")) {
            $tmp = explode("|", $message);
            $result[] = (int)$tmp[1];
            $result[] = $tmp[0];
        }

        return $result;
    }
}
