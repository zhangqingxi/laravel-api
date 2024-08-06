<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Queue\SerializesModels;

class RequestResponseLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Request $request;
    public Response|JsonResponse $response;

    /**
     * Create a new event instance.
     *
     * @param Request $request
     * @param Response|JsonResponse $response
     * @return void
     */
    public function __construct(Request $request, JsonResponse|Response $response)
    {
        //

        $this->request = $request;
        $this->response = $response;
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
