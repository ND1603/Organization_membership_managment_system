<x-app-layout>


<div class="container mx-auto px-4 py-8 max-w-2xl">

    <h1 class="text-2xl font-bold mb-6">Payment Verification Status</h1>

    {{-- Status badge --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm text-gray-500">Submission #{{ $paymentVerification->id }}</span>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold
                {{ $paymentVerification->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                {{ $paymentVerification->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $paymentVerification->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                {{ ucfirst($paymentVerification->status) }}
            </span>
        </div>

        {{-- Extracted data --}}
        <div class="space-y-3">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Transaction ID</span>
                <span class="text-sm font-medium">
                    {{ $paymentVerification->extracted_transaction_id ?? 'Not detected' }}
                </span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Amount</span>
                <span class="text-sm font-medium">
                    {{ $paymentVerification->extracted_amount ? 'ETB ' . number_format($paymentVerification->extracted_amount, 2) : 'Not detected' }}
                </span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm text-gray-500">Date</span>
                <span class="text-sm font-medium">
                    {{ $paymentVerification->extracted_date ? $paymentVerification->extracted_date->format('d M Y') : 'Not detected' }}
                </span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-sm text-gray-500">Submitted</span>
                <span class="text-sm">{{ $paymentVerification->created_at->format('d M Y, H:i') }}</span>
            </div>
        </div>

        {{-- Admin rejection note --}}
        @if($paymentVerification->isRejected() && $paymentVerification->admin_note)
        <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm font-semibold text-red-700 mb-1">Reason for rejection:</p>
            <p class="text-sm text-red-600">{{ $paymentVerification->admin_note }}</p>
        </div>
        @endif
    </div>

    {{-- Screenshot preview --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <h2 class="text-sm font-semibold mb-3">Uploaded Screenshot</h2>
        <img src="{{ Storage::url($paymentVerification->screenshot_path) }}"
             alt="Payment screenshot"
             class="max-w-full rounded-lg border border-gray-200">
    </div>

    <a href="{{ route('payment-verification.create') }}"
       class="inline-block bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
        ← Submit Another
    </a>

</div>
</x-app-layout>