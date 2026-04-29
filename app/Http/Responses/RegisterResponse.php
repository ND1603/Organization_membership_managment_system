<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Fortify calls this after a user is successfully created.
     * Instead of logging them in, we send them to the OTP page.
     */
    public function toResponse($request)
    {
        return redirect()->route('otp.show');
    }
}