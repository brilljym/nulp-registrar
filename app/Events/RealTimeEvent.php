<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RealTimeEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $type;
    public $data;
    public $channels;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $type = 'notification', $data = [], $channels = ['registrar-notifications'])
    {
        $this->message = $message;
        $this->type = $type;
        $this->data = $data;
        $this->channels = is_array($channels) ? $channels : [$channels];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $broadcastChannels = [];
        foreach ($this->channels as $channel) {
            $broadcastChannels[] = new Channel($channel);
        }
        return $broadcastChannels;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'realtime.notification';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'data' => $this->data,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Specify the queue connection to use for broadcasting
     *
     * @return string
     */
    public function broadcastQueue()
    {
        return 'sync';
    }
}