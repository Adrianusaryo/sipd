<?php

namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentStatusUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Document $document, public string $message) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('applicant.'.$this->document->applicant_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'document.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'number_registration' => $this->document->number_registration,
            'status' => $this->document->status,
            'message' => $this->message,
        ];
    }
}
