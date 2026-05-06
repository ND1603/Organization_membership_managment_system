<x-app-layout>


<div class="container mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold mb-6">Payment Verifications</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 rounded p-3 mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 rounded p-3 mb-4">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Member</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Transaction ID</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Amount</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Screenshot</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($verifications as $v)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $v->user->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $v->created_at->format('d M Y H:i') }}</p>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">
                        {{ $v->extracted_transaction_id ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $v->extracted_amount ? 'ETB ' . number_format($v->extracted_amount, 2) : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $v->extracted_date ? $v->extracted_date->format('d M Y') : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ Storage::url($v->screenshot_path) }}" target="_blank"
                           class="text-blue-600 hover:underline text-xs">View image</a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $v->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $v->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $v->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($v->status) }}
                        </span>
                    </td>
                   <td class="px-4 py-3">
                        {{-- Download invoice if approved --}}
                        @if($v->invoice_path)
                            <a href="{{ route('admin.payments.invoice', $v) }}"
                               class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 inline-block mb-2">
                                Download Invoice
                            </a>
                        @endif
                        @if($v->isPending())
                        <div class="flex gap-2">
                            {{-- Approve button --}}
                            <form action="{{ route('admin.payments.approve', $v) }}" method="POST">
                                @csrf
                                <button class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                    Approve
                                </button>
                            </form>

                           

                            {{-- Reject with note --}}
                            <button onclick="showRejectForm({{ $v->id }})"
                                    class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">
                                Reject
                            </button>
                        </div>

                        {{-- Reject form (hidden by default) --}}
                        <form id="reject-form-{{ $v->id }}"
                              action="{{ route('admin.payments.reject', $v) }}"
                              method="POST"
                              class="hidden mt-2">
                            @csrf
                            <textarea name="admin_note" rows="2"
                                      placeholder="Reason for rejection..."
                                      class="w-full border border-gray-200 rounded p-2 text-xs mb-1"
                                      required></textarea>
                            <button class="bg-red-500 text-white px-3 py-1 rounded text-xs">
                                Confirm Reject
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                        No payment verifications yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $verifications->links() }}</div>

</div>

<script>
function showRejectForm(id) {
    document.getElementById('reject-form-' + id).classList.toggle('hidden');
}
</script>
</x-app-layout>