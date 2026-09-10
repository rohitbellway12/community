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
        $query = User::with(['profile.country', 'posts', 'comments']);

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
            $query->where('status', strtolower(trim($request->status)));
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $countries = Country::all();

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'blocked' => User::where('status', 'blocked')->count(),
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
}