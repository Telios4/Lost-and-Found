<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CaseResolution extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'lost_report_id',
        'found_report_id',
        'resolved_by_user',
        'resolved_by_admin',
        'notes',
        'resolved_at',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function lostReport()
    {
        return $this->belongsTo(LostItemReport::class, 'lost_report_id');
    }

    public function foundReport()
    {
        return $this->belongsTo(FoundItemReport::class, 'found_report_id');
    }

    public function userResolver()
    {
        return $this->belongsTo(User::class, 'resolved_by_user');
    }

    public function adminResolver()
    {
        return $this->belongsTo(Admin::class, 'resolved_by_admin');
    }
}
