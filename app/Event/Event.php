<?php

namespace App\Event;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Event
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(public int  $gateId, string $website, string $path )
    {
    }
}
