<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key'             => 'post_liked',
                'label'           => 'Post Liked',
                'description'     => 'Someone liked your post.',
                'is_push_enabled' => true,
                'sort_order'      => 10,
            ],
            [
                'key'             => 'post_commented',
                'label'           => 'New Comment on Post',
                'description'     => 'Someone commented on your post.',
                'is_push_enabled' => true,
                'sort_order'      => 20,
            ],
            [
                'key'             => 'comment_replied',
                'label'           => 'Comment Reply',
                'description'     => 'Someone replied to your comment.',
                'is_push_enabled' => true,
                'sort_order'      => 30,
            ],
            [
                'key'             => 'post_shared',
                'label'           => 'Post Shared',
                'description'     => 'Someone shared your post.',
                'is_push_enabled' => true,
                'sort_order'      => 40,
            ],
            [
                'key'             => 'user_followed',
                'label'           => 'New Follower',
                'description'     => 'Someone started following you.',
                'is_push_enabled' => true,
                'sort_order'      => 50,
            ],
            [
                'key'             => 'new_test',
                'label'           => 'New Test Available',
                'description'     => 'A new test is available for you to take.',
                'is_push_enabled' => true,
                'sort_order'      => 60,
            ],
            [
                'key'             => 'group_invitation',
                'label'           => 'Group Invitation',
                'description'     => 'You were invited to join a group.',
                'is_push_enabled' => true,
                'sort_order'      => 70,
            ],
            [
                'key'             => 'group_join_requested',
                'label'           => 'Group Join Request',
                'description'     => 'Someone requested to join your group.',
                'is_push_enabled' => true,
                'sort_order'      => 80,
            ],
            [
                'key'             => 'group_join_request_accepted',
                'label'           => 'Join Request Accepted',
                'description'     => 'Your group join request was accepted.',
                'is_push_enabled' => true,
                'sort_order'      => 90,
            ],
            [
                'key'             => 'group_join_request_rejected',
                'label'           => 'Join Request Rejected',
                'description'     => 'Your group join request was rejected.',
                'is_push_enabled' => true,
                'sort_order'      => 100,
            ],
            [
                'key'             => 'group_member_removed',
                'label'           => 'Removed from Group',
                'description'     => 'You were removed from a group.',
                'is_push_enabled' => true,
                'sort_order'      => 110,
            ],
        ];

        foreach ($settings as $setting) {
            NotificationSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
