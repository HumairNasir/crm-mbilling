<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'title', 'message', 'icon', 'action_url', 'triggered_by', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    const TYPE_BATCH_STARTED = 'batch_started';
    const TYPE_LEAD_CONVERTED = 'lead_converted';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
}
