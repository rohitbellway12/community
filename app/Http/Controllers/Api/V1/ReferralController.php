<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    use ApiResponse;

    /**
     * Get the authenticated user's referral code, share link,
     * and a list of users they have referred.
     *
     * GET /api/v1/my-referral
     */
    public function myReferral(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Load users referred by this user (paginated)
        $referredUsers = Referral::where('referrer_id', $user->id)
            ->with('referred:id,name,created_at', 'referred.profile:id,user_id,username,avatar')
            ->latest()
            ->paginate(20);

        $shareLink = url('/register?ref=' . $user->referral_code);

        return $this->successResponse([
            'referral_code'  => $user->referral_code,
            'share_link'     => $shareLink,
            'referred_count' => $referredUsers->total(),
            'referred_users' => $referredUsers->map(function ($referral) {
                $referred = $referral->referred;
                $profile  = $referred?->profile;
                $avatar   = $profile?->avatar
                    ? (str_starts_with($profile->avatar, 'http')
                        ? $profile->avatar
                        : asset('storage/' . $profile->avatar))
                    : 'https://ui-avatars.com/api/?name=' . urlencode($referred?->name ?? 'User') . '&background=0D8ABC&color=fff';

                return [
                    'id'         => $referred?->id,
                    'name'       => $referred?->name,
                    'username'   => $profile?->username,
                    'avatar'     => $avatar,
                    'joined_at'  => $referral->created_at?->toIso8601String(),
                ];
            }),
            'pagination' => [
                'current_page' => $referredUsers->currentPage(),
                'last_page'    => $referredUsers->lastPage(),
                'per_page'     => $referredUsers->perPage(),
                'total'        => $referredUsers->total(),
            ],
        ], 'Referral data retrieved successfully.');
    }
}
