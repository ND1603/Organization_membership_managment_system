<?php

namespace App\Actions\Fortify;

use App\Models\Plan;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    // Inject OtpService through the constructor
    public function __construct(protected OtpService $otpService) {}

    public function create(array $input): User
    {
        Validator::make($input, [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'join_date'         => ['nullable', 'date'],
            'address'           => ['nullable', 'string', 'max:500'],
            'phone'             => ['nullable', 'string', 'max:20'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'organization_type' => ['nullable', 'string', 'max:255'],
            'plan_id'           => ['nullable', 'string', 'max:100'],
            'member'            => ['nullable', 'integer'],
            'password'          => $this->passwordRules(),
            'terms'             => Jetstream::hasTermsAndPrivacyPolicyFeature()
                                    ? ['accepted', 'required'] : '',
        ])->validate();

        $defaultPlan = Plan::where('type', 'organAdmin')
                          ->where('name', 'Basic')
                          ->first();

        // Create the user exactly as before
        $user = User::create([
            'name'              => $input['name'],
            'email'             => $input['email'],
            'sex'               => $input['sex'] ?? null,
            'join_date'         => $input['join_date'] ?? null,
            'address'           => $input['address'] ?? null,
            'phone'             => $input['phone'],
            'organization_name' => $input['organization_name'],
            'organization_type' => $input['organization_type'],
            'plan_id'           => $defaultPlan->id,
            'plan_expiry'       => null,
            'role'              => $input['role'] ?? 'organAdmin',
            'member'            => $input['member'],
            'password'          => Hash::make($input['password']),
        ]);

        // Send OTP to user's email
        $this->otpService->send($user->email);

        // Store in session so OtpController knows which user is verifying
        request()->session()->put('otp_user_id', $user->id);
        request()->session()->put('otp_email',   $user->email);

        return $user;
    }
}