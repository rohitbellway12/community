<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\DeviceToken;
use App\Models\Profile;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Handle user registration for Mobile App.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'    => ['required', 'confirmed', Password::defaults()],
            'country_id'  => ['required', 'exists:countries,id'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'fcm_token'   => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $username = Str::slug($validated['name']) . '-' . strtolower(Str::random(5));

        Profile::create([
            'user_id'    => $user->id,
            'username'   => $username,
            'country_id' => $validated['country_id'],
            'joined_at'  => now(),
        ]);

        event(new Registered($user));

        $deviceName = $validated['device_name'] ?? 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->load(['profile.country']);

        $this->saveDeviceToken($user, $validated['fcm_token'] ?? null);

        return $this->successResponse([
            'user'         => new UserResource($user),
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 'Registration successful.', 201);
    }

    /**
     * Handle user login for Mobile App.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => ['required', 'string', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'fcm_token'   => ['nullable', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return $this->errorResponse(
                trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
                429
            );
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            RateLimiter::hit($throttleKey);
            return $this->errorResponse('No account found with this email address.', 401, [
                'email' => ['No account found with this email address.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey);
            return $this->errorResponse('Incorrect password entered.', 401, [
                'password' => ['Incorrect password entered.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        // Account status check (active, suspended, blocked)
        if ($user->status && $user->status !== UserStatus::ACTIVE) {
            return $this->errorResponse(
                'Your account is currently ' . ($user->status->value ?? $user->status) . '. Please contact support.',
                403
            );
        }

        $deviceName = $request->device_name ?: 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->load(['profile.country']);

        $this->saveDeviceToken($user, $request->input('fcm_token'));

        return $this->successResponse([
            'user'         => new UserResource($user),
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 'Login successful.');
    }

    /**
     * Get currently authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['profile.country']);

        return $this->successResponse(
            new UserResource($user),
            'User profile retrieved successfully.'
        );
    }

     /**
     * Revoke current token on logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse([
            'logged_out' => true,
        ], 'Logged out successfully.');
    }

    /**
     * Delete the user's own account.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->tokens()->delete();

        $user->delete();

        return $this->successResponse(null, 'Your account has been deleted successfully.');
    }

    /**
     * Send password reset OTP to user email.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $otp = (string) rand(100000, 999999);
        $cacheKey = 'password_reset_otp_' . strtolower(trim($request->email));

        Cache::put($cacheKey, $otp, now()->addMinutes(15));

        try {
            Mail::raw("Your password reset OTP is: {$otp}. It is valid for 15 minutes.", function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Password Reset OTP Code');
            });
        } catch (\Throwable $e) {
            Log::error('Password reset email error: ' . $e->getMessage());
        }

        $responseData = [
            'email'      => $request->email,
            'expires_in' => '15 minutes',
        ];

        // If local environment, share OTP in response for easier mobile development / testing
        if (config('app.debug')) {
            $responseData['debug_otp'] = $otp;
        }

        return $this->successResponse($responseData, 'OTP has been sent to your email address.');
    }

    /**
     * Verify the sent OTP and return a reset token.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'numeric'],
        ]);

        $email = strtolower(trim($request->email));
        $cacheKey = 'password_reset_otp_' . $email;
        $cachedOtp = Cache::get($cacheKey);

        if (!$cachedOtp || (string) $cachedOtp !== (string) $request->otp) {
            return $this->errorResponse('Invalid or expired OTP.', 422);
        }

        $resetToken = Str::random(60);
        $tokenCacheKey = 'password_reset_token_' . $email;
        Cache::put($tokenCacheKey, $resetToken, now()->addMinutes(15));

        return $this->successResponse([
            'email'       => $email,
            'reset_token' => $resetToken,
        ], 'OTP verified successfully. You can now reset your password.');
    }

    /**
     * Reset password using reset token or valid OTP.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => ['required', 'email', 'exists:users,email'],
            'password'    => ['required', 'confirmed', Password::defaults()],
            'reset_token' => ['nullable', 'string'],
            'otp'         => ['nullable', 'numeric'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $email = strtolower(trim($request->email));
        $tokenCacheKey = 'password_reset_token_' . $email;
        $otpCacheKey = 'password_reset_otp_' . $email;

        $isValid = false;

        if ($request->filled('reset_token')) {
            $cachedToken = Cache::get($tokenCacheKey);
            if ($cachedToken && hash_equals($cachedToken, $request->reset_token)) {
                $isValid = true;
            }
        } elseif ($request->filled('otp')) {
            $cachedOtp = Cache::get($otpCacheKey);
            if ($cachedOtp && (string) $cachedOtp === (string) $request->otp) {
                $isValid = true;
            }
        }

        if (!$isValid) {
            return $this->errorResponse('Invalid or expired reset token/OTP.', 422);
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clear cached OTP & token
        Cache::forget($tokenCacheKey);
        Cache::forget($otpCacheKey);

        // Revoke all previous tokens for security
        $user->tokens()->delete();

        // Create fresh token for seamless auto-login on mobile app
        $deviceName = $request->device_name ?: 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        $user->load(['profile.country']);

        return $this->successResponse([
            'user'         => new UserResource($user),
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 'Password has been reset successfully.');
    }

    /**
     * Change password for logged in user.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $user->load(['profile.country']);

        return $this->successResponse([
            'user' => new UserResource($user),
        ], 'Password updated successfully.');
    }

    /**
     * Save or rotate the user's FCM device token.
     */
    protected function saveDeviceToken($user, ?string $fcmToken): void
    {
        if (!$fcmToken) {
            return;
        }

        try {
            DeviceToken::updateOrCreate(
                ['token' => $fcmToken],
                [
                    'user_id'     => $user->id,
                    'device_type' => 'android',
                    'is_active'   => true,
                    'last_used_at'=> now(),
                ]
            );
        } catch (\Throwable) {
            // Silently ignore token save failures — must not block login/register.
        }
    }
}
