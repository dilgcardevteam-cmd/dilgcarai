<?php

namespace App\Notifications;

use App\Models\Notebook;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotebookSharedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Notebook $notebook,
        protected User $sharedBy,
        protected string $permission,
    ) {}

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
            'title' => 'Notebook shared with you',
            'message' => "{$this->sharedBy->name} shared {$this->notebook->title} with {$this->permission} access.",
            'notebook_id' => $this->notebook->id,
            'route' => route('notebooks.show', $this->notebook),
        ];
    }
}
