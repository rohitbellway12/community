<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['profile.country', 'posts', 'comments', 'latestDeviceToken']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function ($pq) use ($search) {
                      $pq->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = strtolower(trim($request->status));
            if ($status === 'online') {
                $query->whereNotNull('last_seen_at')
                      ->where('last_seen_at', '>=', now()->subMinutes(5))
                      ->orderByDesc('last_seen_at');
            } else {
                $query->where('status', $status);
            }
        }

        // Platform & Device filter (Separating Play Store Android vs App Store iOS)
        if ($request->filled('platform') && $request->platform !== 'all') {
            $platform = strtolower(trim($request->platform));
            if ($platform === 'play_store' || $platform === 'android_app') {
                // Play Store (Android App) Users
                $query->whereHas('deviceTokens', function ($q) {
                    $q->where('is_active', true)
                      ->where('device_type', 'android');
                });
            } elseif ($platform === 'app_store' || $platform === 'ios_app') {
                // App Store (iOS App) Users
                $query->whereHas('deviceTokens', function ($q) {
                    $q->where('is_active', true)
                      ->where('device_type', 'ios');
                });
            } elseif ($platform === 'app') {
                // All Mobile Apps
                $query->whereHas('deviceTokens', function ($q) {
                    $q->where('is_active', true);
                });
            } elseif ($platform === 'web') {
                // Web only users (no active mobile app token)
                $query->whereDoesntHave('deviceTokens', function ($q) {
                    $q->where('is_active', true);
                });
            } elseif ($platform === 'ios_web') {
                // Web users on iPhone / iPad / Mac Safari
                $query->where(function ($q) {
                    $q->where('device_os', 'iOS')
                      ->orWhere('device_os', 'OS X');
                })->whereDoesntHave('deviceTokens', function ($dq) {
                    $dq->where('is_active', true);
                });
            } elseif ($platform === 'android_web') {
                // Web users on Android Phone Browser
                $query->where('device_os', 'Android')
                      ->whereDoesntHave('deviceTokens', function ($dq) {
                          $dq->where('is_active', true);
                      });
            } elseif ($platform === 'desktop') {
                // Desktop Web users
                $query->where(function ($q) {
                    $q->where('device_type', 'Desktop')
                      ->orWhereIn('device_os', ['Windows', 'Linux', 'Ubuntu']);
                });
            }
        }

        $users = ($request->status === 'online' ? $query : $query->latest())->paginate(15)->withQueryString();
        $countries = Country::all();

        $stats = [
            'total'      => User::count(),
            'online'     => User::whereNotNull('last_seen_at')->where('last_seen_at', '>=', now()->subMinutes(5))->count(),
            'active'     => User::where('status', 'active')->count(),
            'blocked'    => User::where('status', 'blocked')->count(),
            'play_store' => User::whereHas('deviceTokens', fn($q) => $q->where('is_active', true)->where('device_type', 'android'))->count(),
            'app_store'  => User::whereHas('deviceTokens', fn($q) => $q->where('is_active', true)->where('device_type', 'ios'))->count(),
            'mobile_app' => User::whereHas('deviceTokens', fn($q) => $q->where('is_active', true))->count(),
            'web_only'   => User::whereDoesntHave('deviceTokens', fn($q) => $q->where('is_active', true))->count(),
            'ios_web'    => User::where(fn($q) => $q->where('device_os', 'iOS')->orWhere('device_os', 'OS X'))->whereDoesntHave('deviceTokens', fn($dq) => $dq->where('is_active', true))->count(),
            'desktop'    => User::where(fn($q) => $q->where('device_type', 'Desktop')->orWhereIn('device_os', ['Windows', 'Linux', 'Ubuntu']))->count(),
        ];

        return view('admin.users', compact('users', 'countries', 'stats'));
    }

    /**
     * Toggle or update user status (active / blocked).
     */
    public function updateStatus(Request $request, User $user)
    {
        $current = is_object($user->status) ? $user->status->value : (string)$user->status;
        $newStatus = $request->input('status');

        if (!$newStatus) {
            $newStatus = ($current === 'active') ? 'blocked' : 'active';
        }

        $user->update([
            'status' => strtolower($newStatus),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "User status updated to {$newStatus}.",
            ]);
        }

        $statusLabel = $newStatus === 'active' ? 'Activated' : 'Deactivated / Blocked';
        return back()->with('success', "User '{$user->name}' is now {$statusLabel}.");
    }

    public function update(Request $request, User$user)
    {
        $profileId =$user->profile?->id;

        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'status'       => 'required|in:active,blocked',
            'role'         => 'nullable|string|max:50',
            'username'     => ['nullable', 'string', 'max:50', Rule::unique('profiles', 'username')->ignore($profileId)],
            'location'     => 'nullable|string|max:100',
            'country_id'   => 'nullable|exists:countries,id',
            'bio'          => 'nullable|string|max:300',
            'avatar'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'remove_avatar'=> 'nullable|boolean',
            'remove_cover' => 'nullable|boolean',
        ], [
            'username.unique' => 'This username is already taken by another user.',
            'email.unique'    => 'This email address is already registered.'
        ]);

        $user->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'status' => strtolower($request->status),
            'role'   => $request->role ?? $user->role,
        ]);

        $profile = $user->profile()->firstOrCreate(['user_id' =>$user->id]);

        $avatarPath =$profile->avatar;
        $coverPath  =$profile->cover_image;

        if ($request->boolean('remove_avatar') &&$avatarPath) {
            Storage::disk('public')->delete($avatarPath);$avatarPath = null;
        }
        if ($request->hasFile('avatar')) {
            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }
            $avatarPath =$request->file('avatar')->store('avatars', 'public');
        }

        if ($request->boolean('remove_cover') &&$coverPath) {
            Storage::disk('public')->delete($coverPath);$coverPath = null;
        }
        if ($request->hasFile('cover_image')) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath =$request->file('cover_image')->store('covers', 'public');
        }

        $profile->update([
            'username'    => $request->username,
            'location'    => $request->location,
            'country_id'  => $request->country_id,
            'bio'         => $request->bio,
            'avatar'      => $avatarPath,
            'cover_image' => $coverPath,
        ]);

        return back()->with('success', 'User details updated successfully.');
    }

    /**
     * Delete a user account and all associated data permanently.
     */
    public function destroy(Request $request, User $user)
    {
        if ((int) auth()->id() === (int) $user->id) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own admin account.'], 403);
            }
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        $userName = $user->name;

        // User::deleting hook in User.php handles all files, notifications, and tokens cleanup
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "User '{$userName}' and all associated records have been permanently deleted."]);
        }

        return back()->with('success', "User '{$userName}' and all associated records have been permanently deleted.");
    }
}