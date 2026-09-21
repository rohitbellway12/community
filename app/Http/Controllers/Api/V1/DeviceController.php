<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    use ApiResponse;

    /**
     * Register or update device information for authenticated user.
     *
     * POST /api/v1/device
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        if (!$user) {
            return $this->errorResponse('Unauthenticated.', 401);
        }

        $validated = $request->validate([
            'device_name'  => ['nullable', 'string', 'max:255'],
            'device_type'  => ['nullable', 'string', 'in:Mobile,Tablet,Desktop'],
            'device_os'    => ['nullable', 'string', 'max:100'],
            'browser'      => ['nullable', 'string', 'max:100'],
            'app_version'  => ['nullable', 'string', 'max:20'],
            'ip_address'   => ['nullable', 'string', 'max:45'],
        ]);

        DB::table('users')->where('id', $user->id)->update([
            'device_type' => $validated['device_type'] ?? $request->header('X-Device-Type'),
            'device_os'   => $validated['device_os'] ?? $request->header('X-Device-OS'),
            'browser'     => $validated['browser'] ?? $request->header('X-Browser'),
            'ip_address'  => $validated['ip_address'] ?? $request->ip(),
        ]);

        $user->refresh();

        return $this->successResponse([
            'device_type' => $user->device_type,
            'device_os'   => $user->device_os,
            'browser'     => $user->browser,
            'ip_address'  => $user->ip_address,
        ], 'Device information saved successfully.');
    }

    /**
     * Get current device/session info for authenticated user.
     *
     * GET /api/v1/device
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');

        if (!$user) {
            return $this->errorResponse('Unauthenticated.', 401);
        }

        return $this->successResponse([
            'device_type' => $user->device_type ?? 'Desktop',
            'device_os'   => $user->device_os,
            'browser'     => $user->browser,
            'ip_address'  => $user->ip_address,
            'last_seen_at' => $user->last_seen_at?->toIso8601String(),
            'is_online'   => $user->isOnline(),
        ], 'Device information retrieved successfully.');
    }
}
