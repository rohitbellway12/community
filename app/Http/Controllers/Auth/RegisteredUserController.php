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
        // Sanitize & normalize referral_code
        $rawReferral = $request->input('referral_code') ?? $request->input('ref');
        if ($rawReferral !== null) {
            $cleanedReferral = trim(strtoupper((string) $rawReferral));
            if ($cleanedReferral === '' || $cleanedReferral === 'NULL' || $cleanedReferral === 'UNDEFINED') {
                $cleanedReferral = null;
            }
            $request->merge(['referral_code' => $cleanedReferral]);
        }

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
                'max:50',
            ],
        ]);

        // Validate referral code if provided (can be a user referral code OR an influencer campaign code)
        $campaignMatch = null;
        $referrer = null;
        if (!empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', $validated['referral_code'])->first();
            $campaignMatch = \App\Models\InfluencerCampaign::where('code', $validated['referral_code'])->first();

            if (!$referrer && !$campaignMatch) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'referral_code' => ['The selected referral code is invalid.'],
                ]);
            }
        }

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

        // Process Influencer Campaign Attribution (via session, cookie, or direct code)
        $influencerCampaignId = session('influencer_campaign_id') ?? request()->cookie('influencer_campaign_id');
        $influencerClickId = session('influencer_click_id') ?? request()->cookie('influencer_click_id');
        $attributedCampaign = null;

        if ($influencerCampaignId) {
            $attributedCampaign = \App\Models\InfluencerCampaign::find($influencerCampaignId);
        }
        if (!$attributedCampaign && $campaignMatch) {
            $attributedCampaign = $campaignMatch;
        }

        if ($attributedCampaign) {
            app(\App\Services\InfluencerTrackingService::class)->attributeRegistration(
                $user,
                $attributedCampaign,
                $influencerClickId ? (int) $influencerClickId : null,
                null,
                $request->ip()
            );

            // Clear session & forget cookies so future signups on this browser are not mistakenly attributed
            session()->forget(['influencer_campaign_id', 'influencer_click_id', 'influencer_code']);
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('influencer_campaign_id'));
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('influencer_click_id'));
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget('influencer_code'));
        }

        // Process peer-to-peer referral if valid user referrer found
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

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('community.index');
    }
}