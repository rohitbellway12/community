<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendAdminPushBroadcastJob;
use App\Models\DeviceToken;
use App\Models\Group;
use App\Models\PushNotification;
use App\Models\PushNotificationLog;
use App\Models\PushNotificationTarget;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminPushNotificationController extends Controller
{
    /**
     * Display a listing of notifications and delivery history.
     */
    public function index(Request $request): View
    {
        $query = PushNotification::with(['creator'])->latest();

        // Filters
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('audience_type')) {
            $query->where('audience_type', $request->audience_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->paginate(15)->withQueryString();

        // High-level statistics
        $stats = [
            'total_campaigns' => PushNotification::count(),
            'total_sent'      => PushNotification::whereIn('status', ['sent', 'partially_failed'])->count(),
            'total_delivered' => (int) PushNotification::sum('success_count'),
            'total_failed'    => (int) PushNotification::sum('failed_count'),
            'total_scheduled' => PushNotification::where('status', 'scheduled')->count(),
        ];

        return view('admin.push-notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Show compose notification screen.
     */
    public function create(): View
    {
        $groups = Group::select('id', 'name', 'slug')
            ->withCount(['users as active_members_count' => function ($q) {
                $q->where('group_user.status', 'active');
            }])
            ->orderBy('name')
            ->get();

        $tests = \App\Models\Test::select('id', 'title')
            ->orderBy('title')
            ->get();

        $events = \App\Models\Event::select('id', 'title')
            ->latest()
            ->limit(30)
            ->get();

        $totalActiveStudents = User::query()
            ->where('status', 'active')
            ->where('role', 'user')
            ->count();

        $activeDeviceTokensCount = DeviceToken::query()
            ->where('is_active', true)
            ->count();

        $countries = \App\Models\Country::where('status', true)
            ->withCount(['profiles as students_count' => function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('status', 'active');
                });
            }])
            ->orderBy('name')
            ->get();

        return view('admin.push-notifications.create', compact('groups', 'tests', 'events', 'countries', 'totalActiveStudents', 'activeDeviceTokensCount'));
    }

    /**
     * Store and dispatch or schedule the push notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:65'],
            'description'       => ['required', 'string', 'max:240'],
            'audience_type'     => ['required', 'in:all_students,group,individual,country'],
            'groups'            => ['required_if:audience_type,group', 'array'],
            'groups.*'          => ['integer', 'exists:groups,id'],
            'users'             => ['required_if:audience_type,individual', 'array'],
            'users.*'           => ['integer', 'exists:users,id'],
            'countries'         => ['required_if:audience_type,country', 'array'],
            'countries.*'       => ['integer', 'exists:countries,id'],
            'image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'], // 1MB
            'banner'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB
            'redirect_type'     => ['nullable', 'in:home,test,group,event,web_url'],
            'redirect_url'      => ['nullable', 'string', 'max:1024'],
            'send_type'         => ['required', 'in:now,schedule'],
            'scheduled_at'      => ['required_if:send_type,schedule', 'nullable', 'date'],
            'user_timezone'     => ['nullable', 'string'],
        ]);

        // Resolve clean action_link for mobile app deep-linking (simple string format: home, test, group, event, or URL)
        $redirectType = $validated['redirect_type'] ?? 'home';
        if ($redirectType === 'web_url' && !empty($validated['redirect_url'])) {
            $actionLink = $validated['redirect_url'];
        } elseif (in_array($redirectType, ['test', 'group', 'event'])) {
            $actionLink = $redirectType;
        } else {
            $actionLink = 'home';
        }

        // Upload media files if provided
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('push-notifications/images', 'public');
            $imageUrl = asset('storage/' . $path);
        }

        $bannerUrl = null;
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('push-notifications/banners', 'public');
            $bannerUrl = asset('storage/' . $path);
        }

        $isScheduled = ($validated['send_type'] === 'schedule');
        $status = $isScheduled ? 'scheduled' : 'draft';
        $scheduledAt = null;

        if ($isScheduled && !empty($validated['scheduled_at'])) {
            try {
                // Input is submitted in KST (Asia/Seoul)
                $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at'], 'Asia/Seoul');
            } catch (\Exception $e) {
                $scheduledAt = \Carbon\Carbon::parse($validated['scheduled_at']);
            }

            // Ensure scheduled time is not in the past in KST (allow 1 minute grace margin)
            if ($scheduledAt->isPast() && $scheduledAt->diffInMinutes(now('Asia/Seoul')) > 1) {
                return back()
                    ->withInput()
                    ->withErrors(['scheduled_at' => 'The scheduled time must be in the future (KST). Please select a future date and time.']);
            }
        }

        $notification = PushNotification::create([
            'title'            => $validated['title'],
            'description'      => $validated['description'],
            'image_url'        => $imageUrl,
            'banner_url'       => $bannerUrl,
            'action_link'      => $actionLink,
            'audience_type'    => $validated['audience_type'],
            'status'           => $status,
            'scheduled_at'     => $scheduledAt,
            'total_recipients' => 0,
            'created_by'       => auth()->id(),
        ]);

        // Store Target Rows
        if ($validated['audience_type'] === 'country' && !empty($validated['countries'])) {
            foreach ($validated['countries'] as $countryId) {
                PushNotificationTarget::create([
                    'notification_id' => $notification->id,
                    'target_type'     => 'country',
                    'target_id'       => $countryId,
                ]);
            }
        } elseif ($validated['audience_type'] === 'group' && !empty($validated['groups'])) {
            foreach ($validated['groups'] as $groupId) {
                PushNotificationTarget::create([
                    'notification_id' => $notification->id,
                    'target_type'     => 'group',
                    'target_id'       => $groupId,
                ]);
            }
        } elseif ($validated['audience_type'] === 'individual' && !empty($validated['users'])) {
            foreach ($validated['users'] as $userId) {
                PushNotificationTarget::create([
                    'notification_id' => $notification->id,
                    'target_type'     => 'user',
                    'target_id'       => $userId,
                ]);
            }
        } else {
            PushNotificationTarget::create([
                'notification_id' => $notification->id,
                'target_type'     => 'all_students',
                'target_id'       => null,
            ]);
        }

        if (!$isScheduled) {
            // Dispatch to queue for instant background delivery without blocking the web page
            SendAdminPushBroadcastJob::dispatch($notification);
            return redirect()
                ->route('admin.push-notifications.show', $notification)
                ->with('success', 'Push notification broadcast has been queued and is being delivered in the background.');
        }

        return redirect()
            ->route('admin.push-notifications.index')
            ->with('success', 'Push notification has been scheduled successfully for ' . $notification->scheduled_at->format('M d, Y h:i A') . '.');
    }

    /**
     * Show notification details and delivery logs.
     */
    public function show(PushNotification $pushNotification, Request $request): View
    {
        $pushNotification->load(['creator', 'targets.group', 'targets.user', 'targets.country']);

        $logsQuery = $pushNotification->logs()->with('user')->latest();

        if ($request->filled('log_status')) {
            $logsQuery->where('status', $request->log_status);
        }

        if ($request->filled('log_search')) {
            $search = trim($request->log_search);
            $logsQuery->where(function ($q) use ($search) {
                $q->where('device_token', 'like', "%{$search}%")
                  ->orWhere('error_message', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $logsQuery->paginate(20)->withQueryString();

        return view('admin.push-notifications.show', compact('pushNotification', 'logs'));
    }

    /**
     * Resend a past or failed notification.
     */
    public function resend(PushNotification $pushNotification): RedirectResponse
    {
        $pushNotification->update([
            'status'        => 'draft',
            'sent_at'       => null,
            'success_count' => 0,
            'failed_count'  => 0,
        ]);

        // Clean previous logs if any
        $pushNotification->logs()->delete();

        SendAdminPushBroadcastJob::dispatch($pushNotification);

        return back()->with('success', 'Notification broadcast has been queued for redelivery in the background.');
    }

    /**
     * Delete notification.
     */
    public function destroy(PushNotification $pushNotification): RedirectResponse
    {
        if ($pushNotification->image_url) {
            $imagePath = str_replace([asset('storage/'), Storage::disk('public')->url(''), 'http://localhost/community/storage/'], '', $pushNotification->image_url);
            Storage::disk('public')->delete(ltrim($imagePath, '/'));
        }

        if ($pushNotification->banner_url) {
            $bannerPath = str_replace([asset('storage/'), Storage::disk('public')->url(''), 'http://localhost/community/storage/'], '', $pushNotification->banner_url);
            Storage::disk('public')->delete(ltrim($bannerPath, '/'));
        }

        $pushNotification->delete();

        return redirect()->route('admin.push-notifications.index')->with('success', 'Notification deleted successfully.');
    }

    /**
     * AJAX endpoint to compute live recipient count.
     */
    public function recipientCount(Request $request): JsonResponse
    {
        $audienceType = $request->input('audience_type', 'all_students');
        $userIds = [];

        if ($audienceType === 'all_students') {
            $userIds = DB::table('device_tokens')
                ->join('users', 'device_tokens.user_id', '=', 'users.id')
                ->where('device_tokens.is_active', true)
                ->where('users.status', 'active')
                ->pluck('users.id')
                ->unique()
                ->all();
        } elseif ($audienceType === 'country') {
            $countryIds = $request->input('countries', []);
            if (!empty($countryIds)) {
                $userIds = DB::table('profiles')
                    ->join('users', 'profiles.user_id', '=', 'users.id')
                    ->join('device_tokens', 'device_tokens.user_id', '=', 'users.id')
                    ->whereIn('profiles.country_id', $countryIds)
                    ->where('users.status', 'active')
                    ->where('device_tokens.is_active', true)
                    ->pluck('users.id')
                    ->unique()
                    ->all();
            }
        } elseif ($audienceType === 'group') {
            $groupIds = $request->input('groups', []);
            if (!empty($groupIds)) {
                $userIds = DB::table('group_user')
                    ->join('users', 'group_user.user_id', '=', 'users.id')
                    ->join('device_tokens', 'device_tokens.user_id', '=', 'users.id')
                    ->whereIn('group_user.group_id', $groupIds)
                    ->where('group_user.status', 'active')
                    ->where('users.status', 'active')
                    ->where('device_tokens.is_active', true)
                    ->pluck('users.id')
                    ->unique()
                    ->all();
            }
        } elseif ($audienceType === 'individual') {
            $requestedUsers = $request->input('users', []);
            if (!empty($requestedUsers)) {
                $userIds = DB::table('device_tokens')
                    ->join('users', 'device_tokens.user_id', '=', 'users.id')
                    ->whereIn('users.id', $requestedUsers)
                    ->where('users.status', 'active')
                    ->where('device_tokens.is_active', true)
                    ->pluck('users.id')
                    ->unique()
                    ->all();
            }
        }

        $userCount = count($userIds);
        $tokenCount = 0;

        if ($userCount > 0) {
            $tokenCount = DeviceToken::query()
                ->whereIn('user_id', $userIds)
                ->where('is_active', true)
                ->count();
        }

        return response()->json([
            'success'     => true,
            'user_count'  => $userCount,
            'token_count' => $tokenCount,
            'message'     => "{$userCount} active students (" . ($tokenCount > 0 ? "{$tokenCount} active devices" : "0 devices registered") . ") will receive this notification.",
        ]);
    }

    /**
     * AJAX endpoint to search active students for individual audience selection.
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['items' => []]);
        }

        $users = User::query()
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->whereHas('deviceTokens', function ($dq) {
                $dq->where('is_active', true);
            })
            ->withCount(['deviceTokens' => function ($dq) {
                $dq->where('is_active', true);
            }])
            ->limit(20)
            ->get(['id', 'name', 'email']);

        $formatted = $users->map(function ($u) {
            return [
                'id'           => $u->id,
                'name'         => $u->name,
                'email'        => $u->email,
                'device_count' => $u->device_tokens_count,
            ];
        });

        return response()->json(['items' => $formatted]);
    }
}
