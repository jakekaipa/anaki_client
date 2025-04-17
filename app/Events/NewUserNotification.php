<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\UserNotification;

class NewUserNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userNotification;

    public function __construct(UserNotification $userNotification)
    {
        $this->userNotification = $userNotification;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userNotification->user_id);
    }

    public function broadcastAs()
    {
        return 'new.notification';
    }
}
