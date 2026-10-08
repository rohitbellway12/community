<?php

namespace App\Services;

use App\Models\ProfileActivity;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProfileActivityService
{
    /**
     * Get all active activities ordered by sort_order.
     */
    public function getActiveActivities(): Collection
    {
        return ProfileActivity::active()->get();
    }

    /**
     * Catalog of all system-supported triggers with auto-configured target URLs.
     */
    public static function getAvailableTriggers(): array
    {
        return [
            'avatar' => [
                'title'          => 'Upload Profile Photo',
                'description'    => 'Add a personalized profile avatar to help members recognize you.',
                'action_url'     => '/profile/edit',
                'action_label'   => 'Upload Avatar',
                'default_points' => 15,
                'icon'           => 'camera',
            ],
            'bio' => [
                'title'          => 'Write a Bio',
                'description'    => 'Tell the community about your goals, study interests, and background.',
                'action_url'     => '/profile/edit',
                'action_label'   => 'Write Bio',
                'default_points' => 10,
                'icon'           => 'user',
            ],
            'location' => [
                'title'          => 'Set Country & Location',
                'description'    => 'Select your home country or city to connect with nearby members.',
                'action_url'     => '/profile/edit',
                'action_label'   => 'Set Location',
                'default_points' => 10,
                'icon'           => 'globe',
            ],
            'bio_location' => [
                'title'          => 'Add Bio & Location',
                'description'    => 'Complete both your personal bio and location details in full.',
                'action_url'     => '/profile/edit',
                'action_label'   => 'Edit Profile',
                'default_points' => 20,
                'icon'           => 'user',
            ],
            'email_verified' => [
                'title'          => 'Verify Email Address',
                'description'    => 'Confirm your email address to secure your account and receive updates.',
                'action_url'     => '/email/verify',
                'action_label'   => 'Verify Email',
                'default_points' => 20,
                'icon'           => 'mail',
            ],
            'first_post' => [
                'title'          => 'Publish Your First Post',
                'description'    => 'Start a meaningful discussion or share knowledge with the community.',
                'action_url'     => '/community',
                'action_label'   => 'Create Post',
                'default_points' => 15,
                'icon'           => 'pencil',
            ],
            'first_comment' => [
                'title'          => 'Post a Helpful Comment',
                'description'    => 'Engage with another member by commenting on their discussion.',
                'action_url'     => '/community',
                'action_label'   => 'Explore Posts',
                'default_points' => 10,
                'icon'           => 'chat',
            ],
            'join_group' => [
                'title'          => 'Join a Community Group',
                'description'    => 'Become a member of at least one study or regional community group.',
                'action_url'     => '/groups',
                'action_label'   => 'Browse Groups',
                'default_points' => 10,
                'icon'           => 'users',
            ],
            'first_test' => [
                'title'          => 'Attempt an Online Test',
                'description'    => 'Take at least one student test to test your skills and earn scores.',
                'action_url'     => '/tests/student',
                'action_label'   => 'Take Test',
                'default_points' => 20,
                'icon'           => 'academic-cap',
            ],
            'follow_user' => [
                'title'          => 'Follow Another Member',
                'description'    => 'Connect with peers by following at least one fellow community member.',
                'action_url'     => '/community',
                'action_label'   => 'Find Members',
                'default_points' => 10,
                'icon'           => 'user-add',
            ],
            'cover_image' => [
                'title'          => 'Upload Profile Banner',
                'description'    => 'Add a personalized header cover image to make your profile stand out.',
                'action_url'     => '/profile/edit',
                'action_label'   => 'Upload Banner',
                'default_points' => 10,
                'icon'           => 'photograph',
            ],
            'like_post' => [
                'title'          => 'Like a Community Post',
                'description'    => 'Appreciate other members by liking at least one post or discussion.',
                'action_url'     => '/community',
                'action_label'   => 'Explore & Like',
                'default_points' => 5,
                'icon'           => 'heart',
            ],
            'save_post' => [
                'title'          => 'Bookmark / Save a Post',
                'description'    => 'Save an interesting discussion or study resource to your reading list.',
                'action_url'     => '/community',
                'action_label'   => 'Save a Post',
                'default_points' => 5,
                'icon'           => 'bookmark',
            ],
            'share_post' => [
                'title'          => 'Share a Discussion Post',
                'description'    => 'Share a community discussion link or post to help others learn.',
                'action_url'     => '/community',
                'action_label'   => 'Share Post',
                'default_points' => 5,
                'icon'           => 'share',
            ],
            'pass_test' => [
                'title'          => 'Pass an Online Test',
                'description'    => 'Score a passing result on any online student practice or mock test.',
                'action_url'     => '/tests/student',
                'action_label'   => 'Pass a Test',
                'default_points' => 20,
                'icon'           => 'academic-cap',
            ],
        ];
    }

    /**
     * Calculate completion progress for a given user.
     */
    public function calculateProgress(?User $user): array
    {
        $activities = $this->getActiveActivities();

        if (!$user) {
            return [
                'percentage'       => 0,
                'earned_points'    => 0,
                'total_points'     => (int) $activities->sum('points'),
                'completed_count'  => 0,
                'total_count'      => $activities->count(),
                'activities'       => $activities->map(fn($a) => [
                    'id'           => $a->id,
                    'key'          => $a->key,
                    'title'        => $a->title,
                    'description'  => $a->description,
                    'points'       => (int) $a->points,
                    'action_url'   => $a->action_url,
                    'action_label' => $a->action_label ?: 'Complete',
                    'icon'         => $a->icon,
                    'is_completed' => false,
                ])->values()->all(),
            ];
        }

        // Preload relationships if not already loaded
        $user->loadMissing(['profile']);

        // Check common activity triggers for this user
        $hasPosts = DB::table('posts')->where('user_id', $user->id)->whereNull('deleted_at')->exists();
        $hasComments = DB::table('comments')->where('user_id', $user->id)->whereNull('deleted_at')->exists();
        $hasTests = DB::table('test_attempts')->where('user_id', $user->id)->where('status', 'completed')->exists();
        $hasPassedTest = DB::table('test_attempts')->where('user_id', $user->id)->where('status', 'completed')->where('result', 'pass')->exists();
        $hasFollows = DB::table('follows')->where('follower_id', $user->id)->exists();
        $hasGroups = DB::table('group_user')->where('user_id', $user->id)->exists();
        $hasLikes = DB::table('likes')->where('user_id', $user->id)->exists();
        $hasSavedPosts = DB::table('saved_posts')->where('user_id', $user->id)->exists();
        $hasShares = DB::table('shares')->where('user_id', $user->id)->exists();

        $profile = $user->profile;
        $hasAvatar = !empty($profile?->avatar);
        $hasCover = !empty($profile?->cover_image);
        $hasBio = !empty(trim((string)($profile?->bio ?? '')));
        $hasLocation = !empty(trim((string)($profile?->location ?? $profile?->city ?? ''))) || !empty($profile?->country_id);
        $hasVerifiedEmail = !empty($user->email_verified_at);

        $earnedPoints = 0;
        $totalPoints = 0;
        $completedCount = 0;

        $items = [];

        foreach ($activities as $act) {
            $isCompleted = false;

            switch ($act->key) {
                case 'avatar':
                    $isCompleted = $hasAvatar;
                    break;
                case 'cover_image':
                    $isCompleted = $hasCover;
                    break;
                case 'bio_location':
                    // Requires BOTH bio AND location/country
                    $isCompleted = $hasBio && $hasLocation;
                    break;
                case 'bio':
                    $isCompleted = $hasBio;
                    break;
                case 'location':
                    $isCompleted = $hasLocation;
                    break;
                case 'email_verified':
                    $isCompleted = $hasVerifiedEmail;
                    break;
                case 'first_post':
                    $isCompleted = $hasPosts;
                    break;
                case 'first_comment':
                    $isCompleted = $hasComments;
                    break;
                case 'join_group':
                    $isCompleted = $hasGroups;
                    break;
                case 'first_test':
                    $isCompleted = $hasTests;
                    break;
                case 'pass_test':
                    $isCompleted = $hasPassedTest;
                    break;
                case 'follow_user':
                    $isCompleted = $hasFollows;
                    break;
                case 'like_post':
                    $isCompleted = $hasLikes;
                    break;
                case 'save_post':
                    $isCompleted = $hasSavedPosts;
                    break;
                case 'share_post':
                    $isCompleted = $hasShares;
                    break;
                default:
                    $isCompleted = false;
                    break;
            }

            $totalPoints += (int) $act->points;
            if ($isCompleted) {
                $earnedPoints += (int) $act->points;
                $completedCount++;
            }

            $items[] = [
                'id'           => $act->id,
                'key'          => $act->key,
                'title'        => $act->title,
                'description'  => $act->description,
                'points'       => (int) $act->points,
                'action_url'   => $act->action_url,
                'action_label' => $act->action_label ?: 'Complete',
                'icon'         => $act->icon,
                'is_completed' => $isCompleted,
            ];
        }

        $percentage = $totalPoints > 0 ? (int) round(($earnedPoints / $totalPoints) * 100) : 0;
        $percentage = min(100, max(0, $percentage));

        return [
            'percentage'      => $percentage,
            'earned_points'   => $earnedPoints,
            'total_points'    => $totalPoints,
            'completed_count' => $completedCount,
            'total_count'     => count($items),
            'activities'      => $items,
        ];
    }
}
