<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
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
        private readonly int $score
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Potential item match found',
            'target_type' => $this->targetType,
            'target_id' => $this->targetId,
            'target_name' => $this->targetName,
            'match_type' => $this->matchType,
            'match_id' => $this->matchId,
            'match_name' => $this->matchName,
            'score' => $this->score,
            'message' => "A potential {$this->score}% match was detected between {$this->targetName} and {$this->matchName}.",
            'url' => route('reports.show', ['type' => $this->targetType, 'id' => $this->targetId]),
        ];
    }
}
