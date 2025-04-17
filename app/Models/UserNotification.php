<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Events\NewUserNotification;

class UserNotification extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'user_id' => 'integer',
        'sender_id' => 'integer',
        'order_id' => 'integer',
        'type' => 'string',
        'message' => 'object',
        'is_read' => 'boolean',
    ];

    protected $with = [
        'user',
        'sender',
    ];

    protected $dispatchesEvents = [
        'created' => NewUserNotification::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function scopeGetByType($query, $types)
    {
        if (is_array($types)) return $query->whereIn('type', $types);
    }

    public function scopeNotAuth($query)
    {
        $query->where("user_id", "!=", auth()->user()->id);
    }

    public function scopeAuth($query)
    {
        $query->where("user_id", Auth::guard(get_auth_guard())->user()->id);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
