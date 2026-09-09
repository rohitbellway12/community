<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    // Step ke mutabiq view render karein
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->has('reset')) {
            session()->forget(['reset_email', 'otp_code', 'otp_verified', 'step']);
            return redirect()->route('password.request');
        }

        $step = session('step', 1);

        return view('auth.forgot-password', compact('step'));
    }

    // Top par facade import hona chahiye

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = rand(100000, 999999);

        session([
            'reset_email' => $request->email,
            'otp_code' => $otp,
            'step' => 2,
        ]);

        // Real Email Bhejne ke liye yeh code uncomment karein:
        Mail::raw("Your password reset OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Password Reset OTP Code');
        });

        return redirect()->route('password.request')->with('status', 'OTP has been sent to your email address.');
    }

    // Step 2: OTP Verify Karein
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        if ($request->otp == session('otp_code')) {
            session(['otp_verified' => true, 'step' => 3]);

            return redirect()->route('password.request')->with('status', 'OTP verified successfully. Create your new password.');
        }

        return redirect()->route('password.request')->withErrors(['otp' => 'Invalid OTP entered.']);
    }

    // Step 3: Password Update Karein
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'New password and confirm password do not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $email = $request->input('email') ?: session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['general' => 'Session expired. Please request OTP again.']);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Session clear karein
            session()->forget(['reset_email', 'otp_code', 'otp_verified', 'step']);

            return redirect()->route('login')->with('status', 'Password updated successfully! Please login with your new password.');
        }

        return redirect()->route('password.request')
            ->withErrors(['general' => 'User not found with this email.']);
    }
}
