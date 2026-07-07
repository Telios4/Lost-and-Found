<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PotentialMatchFound extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $targetType,
        private readonly string $targetId,
        private readonly string $targetName,
        private readonly string $matchType,
        private readonly string $matchId,
        private readonly string $matchName,
        private readonly int $score,
        private readonly bool $adminManagedMatch = false
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toArray(object $notifiable): array
    {
        $message = "A potential {$this->score}% match was detected between {$this->targetName} and {$this->matchName}.";

        if ($this->adminManagedMatch) {
            $message .= ' This found item was posted by the Lost and Found admin team. Visit the lost and found security office in Central Building Basement to claim your item.';
        }

        return [
            'title' => 'Potential item match found',
            'target_type' => $this->targetType,
            'target_id' => $this->targetId,
            'target_name' => $this->targetName,
            'match_type' => $this->matchType,
            'match_id' => $this->matchId,
            'match_name' => $this->matchName,
            'score' => $this->score,
            'is_admin_managed_match' => $this->adminManagedMatch,
            'message' => $message,
            'url' => route('reports.show', ['type' => $this->targetType, 'id' => $this->targetId]),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Lost & Found: Potential Match Detected')
            ->greeting('Hello ' . ($notifiable->name ?? 'there') . ',')
            ->line("A potential {$this->score}% match was detected between {$this->targetName} and {$this->matchName}.")
            ->action('View Match Details', route('reports.show', ['type' => $this->targetType, 'id' => $this->targetId]));

        if ($this->adminManagedMatch) {
            $mail->line('This found item was posted by the Lost and Found admin team.')
                ->line('Please visit the lost and found security office in Central Building Basement to claim your item.');
        }

        return $mail->line('You are receiving this email because match alerts are enabled for your account activity.');
    }
}
