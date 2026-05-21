<?php

namespace App\Notifications;

use App\Models\Source;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SourceProcessedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Source $source) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Source indexed',
            'message' => "{$this->source->name} is now indexed and available in AI chat.",
            'notebook_id' => $this->source->notebook_id,
            'route' => route('notebooks.show', $this->source->notebook),
        ];
    }
}
