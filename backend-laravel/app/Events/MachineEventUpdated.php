<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MachineEventUpdated implements ShouldBroadcastNow
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new Channel('machine-events');
    }

    public function broadcastAs()
    {
        return 'machine.updated';
    }
}