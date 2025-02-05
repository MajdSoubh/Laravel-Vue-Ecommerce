<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $queue = 'notifications';

    /**
     * Create a new event instance.
     */
    public function __construct(private int $userId, private string $action, private array $data = [])
    {
    }

    public function broadcastWith(): array
    {
        return [
            'data' => $this->action === 'clear' ? [] : $this->data,
            'action' => $this->action,
        ];
    }

    public function broadcastAs(): string
    {
        return 'cart';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel("user." . $this->userId);
    }
}
