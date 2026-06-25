<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Message extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sender_id',
        'receiver_id',
        'lost_item_report_id',
        'found_item_report_id',
        'body',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Message $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function lostReport(): BelongsTo
    {
        return $this->belongsTo(LostItemReport::class, 'lost_item_report_id');
    }

    public function foundReport(): BelongsTo
    {
        return $this->belongsTo(FoundItemReport::class, 'found_item_report_id');
    }
}
