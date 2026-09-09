<?php namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class GroupJoinRequestNotification extends Notification
{
    use Queueable;

    public $group;
    public $user;

    public function __construct($group, $user)
    {
        $this->group = $group;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // Database se current user aur group ka status check karein
        $groupUser = DB::table('group_user')
            ->where('group_id', $this->group->id)
            ->where('user_id', $this->user->id)
            ->first();

        return [
            'type' => 'group_join_requested',
            'group_id' => $this->group->id,
            'group_slug' => $this->group->slug,
            'group_name' => $this->group->name,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'status' => $groupUser ? $groupUser->status : 'pending', // Yeh line status bhejegi
            'message' => "{$this->user->name} wants to join your group '{$this->group->name}'.",
        ];
    }
}