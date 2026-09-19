<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    use ApiResponse;

    /**
     * Register / update a device token for push notifications.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token'   => ['required', 'string'],
            'device_type' => ['nullable', 'in:android,ios'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ]);

        $user = $request->user();

        DeviceToken::updateOrCreate(
            ['token' => $validated['fcm_token']],
            [
                'user_id'     => $user->id,
                'device_type' => $validated['device_type'] ?? 'android',
                'app_version' => $validated['app_version'],
                'is_active'   => true,
                'last_used_at'=> now(),
            ]
        );

        return $this->successResponse(
            ['registered' => true],
            'Device token registered successfully.',
            201
        );
    }

    /**
     * List the current user's active device tokens.
     */
    public function index(Request $request): JsonResponse
    {
        $tokens = $request->user()
            ->deviceTokens()
            ->active()
            ->orderBy('last_used_at', 'desc')
            ->get();

        return $this->successResponse($tokens, 'Device tokens retrieved successfully.');
    }

    /**
     * Deactivate (soft) a device token.
     */
    public function destroy(Request $request, DeviceToken $deviceToken): JsonResponse
    {
        $user = $request->user();

        if ((int) $deviceToken->user_id !== (int) $user->id) {
            return $this->errorResponse('You can only manage your own device tokens.', 403);
        }

        $deviceToken->update(['is_active' => false]);

        return $this->successResponse(null, 'Device token removed successfully.');
    }
}
