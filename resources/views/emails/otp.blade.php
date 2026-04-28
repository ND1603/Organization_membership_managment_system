@component('mail::message')
# Verify Your Account

Hello,

You requested to create an account at **{{ config('app.name') }}**.

Your one-time verification code is:

@component('mail::panel')
<div style="text-align:center; font-size:36px; font-weight:bold; letter-spacing:10px;">
{{ $otp }}
</div>
@endcomponent

**This code expires in 10 minutes.**

Do not share this code with anyone.

Thanks,
{{ config('app.name') }}
@endcomponent