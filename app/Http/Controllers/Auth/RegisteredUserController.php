<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Referral;
use App\Models\User;
use App\Models\UserStat;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class
            ],
            'password'      => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
            'country_id'    => [
                'required',
                'exists:countries,id'
            ],
            'referral_code' => [
                'nullable',
                'string',
                'max:15',
                'exists:users,referral_code'
            ],
        ]);

        // Auto-generate a unique referral code: REIAC + 5 digits
        do {
            $code = 'REIAC' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (User::where('referral_code', $code)->exists());

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'referral_code' => $code,
        ]);

        $username = Str::slug($validated['name']) . '-' . strtolower(Str::random(5));

        Profile::create([
            'user_id'    => $user->id,
            'username'   => $username,
            'country_id' => $validated['country_id'],
            'joined_at'  => now(),
        ]);

        // Initialize user stats (with referred_count = 0)
        UserStat::firstOrCreate(['user_id' => $user->id]);

        // Process referral if a code was provided
        if (!empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', $validated['referral_code'])->first();
            if ($referrer && $referrer->id !== $user->id) {
                Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $user->id,
                ]);

                // Increment the referrer's referred_count in user_stats
                UserStat::updateOrCreate(
                    ['user_id' => $referrer->id],
                    []
                )->increment('referred_count');
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('community.index');
    }
}