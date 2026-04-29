<x-app-layout>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-1">Check your email</h4>
                        <p class="text-muted mb-4">
                            We sent a 6-digit verification code to
                            <strong>{{ session('otp_email') }}</strong>.
                            Enter it below to activate your account.
                        </p>

                        @if(session('info'))
                            <div class="alert alert-info py-2">{{ session('info') }}</div>
                        @endif
                        @if(session('success'))
                            <div class="alert alert-success py-2">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('otp.verify') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Verification Code</label>
                                <input
                                    type="text"
                                    name="otp"
                                    maxlength="6"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    autofocus
                                    class="form-control form-control-lg text-center fs-3 fw-bold
                                           @error('otp') is-invalid @enderror"
                                    placeholder="000000"
                                    value="{{ old('otp') }}"
                                >
                                @error('otp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Verify & Continue
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <p class="text-muted small mb-1">Didn't receive the code?</p>
                            <form method="POST" action="{{ route('otp.resend') }}">
                                @csrf
                                <button type="submit" class="btn btn-link btn-sm p-0">
                                    Resend verification code
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>