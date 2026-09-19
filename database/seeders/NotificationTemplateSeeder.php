<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'event'       => 'post_liked',
                'label'       => 'Post Liked',
                'description' => 'When someone likes your post.',
                'title'       => 'Post Liked',
                'body'        => ":user_name liked your post ':post_title'.",
                'sort_order'  => 10,
            ],
            [
                'event'       => 'post_commented',
                'label'       => 'New Comment on Post',
                'description' => 'When someone comments on your post.',
                'title'       => 'New Comment',
                'body'        => ":user_name commented on your post ':post_title'.",
                'sort_order'  => 20,
            ],
            [
                'event'       => 'comment_replied',
                'label'       => 'Comment Reply',
                'description' => 'When someone replies to your comment.',
                'title'       => 'New Reply',
                'body'        => ":user_name replied to your comment on ':post_title'.",
                'sort_order'  => 30,
            ],
            [
                'event'       => 'post_shared',
                'label'       => 'Post Shared',
                'description' => 'When someone shares your post.',
                'title'       => 'Post Shared',
                'body'        => ":user_name shared your post ':post_title'.",
                'sort_order'  => 40,
            ],
            [
                'event'       => 'user_followed',
                'label'       => 'New Follower',
                'description' => 'When someone starts following you.',
                'title'       => 'New Follower',
                'body'        => ":follower_name started following you.",
                'sort_order'  => 50,
            ],
            [
                'event'       => 'new_test',
                'label'       => 'New Test Available',
                'description' => 'When a new test is published.',
                'title'       => 'New Test Available',
                'body'        => "New :level_name test ':test_title' is now available.",
                'sort_order'  => 60,
            ],
            [
                'event'       => 'group_invitation',
                'label'       => 'Group Invitation',
                'description' => 'When you are invited to a group.',
                'title'       => 'Group Invitation',
                'body'        => ":invited_by_name invited you to join ':group_name'.",
                'sort_order'  => 70,
            ],
            [
                'event'       => 'group_join_requested',
                'label'       => 'Group Join Request',
                'description' => 'When someone requests to join your group.',
                'title'       => 'Group Join Request',
                'body'        => ":user_name wants to join your group ':group_name'.",
                'sort_order'  => 80,
            ],
            [
                'event'       => 'group_join_request_accepted',
                'label'       => 'Join Request Accepted',
                'description' => 'When your group join request is accepted.',
                'title'       => 'Join Request Accepted',
                'body'        => "Your request to join ':group_name' has been accepted.",
                'sort_order'  => 90,
            ],
            [
                'event'       => 'group_join_request_rejected',
                'label'       => 'Join Request Rejected',
                'description' => 'When your group join request is rejected.',
                'title'       => 'Join Request Rejected',
                'body'        => "Your request to join ':group_name' has been rejected.",
                'sort_order'  => 100,
            ],
            [
                'event'       => 'group_member_removed',
                'label'       => 'Removed from Group',
                'description' => 'When you are removed from a group.',
                'title'       => 'Removed from Group',
                'body'        => "You were removed from ':group_name'.",
                'sort_order'  => 110,
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['event' => $template['event']],
                $template
            );
        }
    }
}
