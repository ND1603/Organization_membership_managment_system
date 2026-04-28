<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate a new OTP, save it hashed, and email it.
     */
    public function send(string $email): void
    {
        // Invalidate any previous unused OTPs for this email
        // so old codes stop working when a new one is requested
        OtpVerification::where('identifier', $email)
            ->where('type', 'email')
            ->where('used', false)
            ->update(['used' => true]);

        // Generate a random 6-digit code
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save the hashed version — never store raw OTPs
        OtpVerification::create([
            'identifier' => $email,
            'type'       => 'email',
            'otp'        => hash('sha256', $otp),
            'expires_at' => now()->addMinutes(10),
            'used'       => false,
        ]);

        // Send the plain code to the user's email
        Mail::to($email)->send(new OtpMail($otp));
    }

    /**
     * Check if the code the user typed is correct.
     * Returns true if valid, false if wrong or expired.
     */
    public function verify(string $email, string $otp): bool
    {
        $record = OtpVerification::where('identifier', $email)
            ->where('type', 'email')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$record) {
            return false;
        }

        // hash_equals is timing-safe — prevents brute force timing attacks
        $isValid = hash_equals($record->otp, hash('sha256', $otp));

        if ($isValid) {
            $record->update(['used' => true]);
        }

        return $isValid;
    }
}