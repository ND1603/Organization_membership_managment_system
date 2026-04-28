<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Show the OTP entry page.
     * If no session exists, send them back to register.
     */
    public function show()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('register')
                ->with('error', 'Please complete registration first.');
        }

        return view('auth.otp');
    }

    /**
     * Handle the OTP form submission.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');
        $email  = session('otp_email');

        if (!$userId || !$email) {
            return redirect()->route('register')
                ->with('error', 'Session expired. Please register again.');
        }

        $isValid = $this->otpService->verify($email, $request->otp);

        if (!$isValid) {
            return back()->withErrors([
                'otp' => 'The code is incorrect or has expired. Please try again.',
            ]);
        }

        // Mark user as verified
        $user = User::findOrFail($userId);
        $user->update([
            'phone_verified'    => true,
            'phone_verified_at' => now(),
        ]);

        // Clear OTP session data
        session()->forget(['otp_user_id', 'otp_email']);

        // Log the user in now
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Account verified! Welcome to ' . config('app.name'));
    }

    /**
     * Resend a fresh OTP to the same email.
     */
    public function resend(Request $request)
    {
        $userId = session('otp_user_id');
        $email  = session('otp_email');

        if (!$userId || !$email) {
            return redirect()->route('register');
        }

        $this->otpService->send($email);

        return back()->with('success', 'A new code has been sent to ' . $email);
    }
}