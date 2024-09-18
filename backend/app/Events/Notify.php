<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class Notify implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public string $message, public string $type = 'success', public ?array $data = null)
    {
        //
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        if (auth('sanctum')->user())
            return new PrivateChannel("user." . auth('sanctum')->user()->id);
        else
        {
            return new Channel("user." . request()->header('X-Unique-ID'));
        }
    }
}
