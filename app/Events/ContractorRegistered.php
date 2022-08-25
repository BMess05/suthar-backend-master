<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Contractor;
use Illuminate\Support\Facades\Log;

class ContractorRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $contractor;
    public $password;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Contractor $contractor, $password)
    {
        $this->contractor = $contractor;
        $this->password = $password;
        Log::info("Contractor Added, Event Called: " . $this->contractor->email);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
