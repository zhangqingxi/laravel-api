<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ModelLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $content;
    public int $adminId;
    public string $tableName;
    public int $tableId;

    /**
     * Create a new event instance.
     */
    public function __construct(array $content, string $tableName, int $tableId)
    {
        //
        $this->content = $content;

        $this->tableName = $tableName;

        $this->tableId = $tableId;

        $this->adminId = Auth::guard('admin')->id() ?? 0;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
