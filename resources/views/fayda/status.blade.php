<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                {{-- Messages --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Fayda National ID Verification</div>
                    <div class="card-body">

                        @if(Auth::user()->fayda_verified)
                            {{-- Already verified --}}
                            <div class="text-center py-3">
                                <div style="font-size: 48px;">✅</div>
                                <h5 class="mt-3 text-success fw-bold">Identity Verified</h5>
                                <p class="text-muted">Your Fayda ID has been successfully verified.</p>

                                <div class="bg-light rounded p-3 mt-3 text-start">
                                    <small class="text-muted d-block">Fayda FIN</small>
                                    <code>{{ Auth::user()->fayda_fin }}</code>

                                    <small class="text-muted d-block mt-2">Verified At</small>
                                    <span>{{ Auth::user()->fayda_verified_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>

                        @else
                            {{-- Not yet verified --}}
                            <div class="text-center py-3">
                                <div style="font-size: 48px;">🪪</div>
                                <h5 class="mt-3 fw-bold">Verify Your Identity</h5>
                                <p class="text-muted mb-4">
                                    Connect your Fayda National ID to verify your identity.
                                    This helps keep the organization secure.
                                </p>

                                <a href="{{ route('fayda.redirect') }}" class="btn btn-primary px-4 py-2">
                                    Verify with Fayda ID →
                                </a>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>