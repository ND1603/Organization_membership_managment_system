<x-app-layout>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                {{-- Success/Info messages --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Payment Form --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-bold">
                        Pay with Telebirr
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            You will be redirected to Telebirr to complete your payment securely.
                        </p>

                        <form method="POST" action="{{ route('telebirr.initiate') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Amount (ETB)</label>
                                <input
                                    type="number"
                                    name="amount"
                                    min="1"
                                    step="0.01"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    placeholder="e.g. 500"
                                    value="{{ old('amount') }}"
                                    required
                                >
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description (optional)</label>
                                <input
                                    type="text"
                                    name="description"
                                    class="form-control"
                                    placeholder="e.g. Annual membership fee"
                                    value="{{ old('description') }}"
                                >
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                Pay with Telebirr →
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Payment History --}}
                @if($payments->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Recent Payments</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td><code>{{ $payment->out_trade_no }}</code></td>
                                    <td>ETB {{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @if($payment->isPaid())
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($payment->isFailed())
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>