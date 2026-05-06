<x-app-layout>

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">

    <h1 class="text-2xl font-bold mb-2">Submit Payment Proof</h1>
    <p class="text-gray-600 mb-6">Upload a screenshot of your Telebirr or CBE Birr payment confirmation.</p>

    {{-- Success/Error messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 rounded p-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Upload form --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <form action="{{ route('payment-verification.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Payment Screenshot
                </label>

                {{-- Drag and drop area --}}
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors"
                     id="dropzone">
                    <input type="file"
                           name="screenshot"
                           id="screenshot"
                           accept="image/*"
                           class="hidden"
                           onchange="previewImage(event)">

                    <div id="upload-prompt">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                        <p class="text-gray-400 text-sm">PNG, JPG, WebP — max 5MB</p>
                        <button type="button"
                                onclick="document.getElementById('screenshot').click()"
                                class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                            Choose File
                        </button>
                    </div>

                    {{-- Preview area (hidden until file selected) --}}
                    <div id="preview-area" class="hidden">
                        <img id="preview-img" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg mb-3">
                        <p id="preview-name" class="text-sm text-gray-600"></p>
                        <button type="button"
                                onclick="clearImage()"
                                class="mt-2 text-red-500 text-sm hover:underline">
                            Remove
                        </button>
                    </div>
                </div>

                {{-- Validation error --}}
                @error('screenshot')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800 font-semibold mb-1">Tips for a clear screenshot:</p>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Make sure the Transaction ID is fully visible</li>
                    <li>• Include the amount and date in the screenshot</li>
                    <li>• Avoid cropping or editing the image</li>
                    <li>• Good lighting if photographing a screen</li>
                </ul>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Upload & Verify Payment
            </button>
        </form>
    </div>

    {{-- Previous submissions --}}
    @if($submissions->count() > 0)
    <div>
        <h2 class="text-lg font-semibold mb-3">Your Previous Submissions</h2>
        <div class="space-y-3">
            @foreach($submissions as $sub)
            <div class="bg-white border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                <div>
                    <p class="text-sm font-medium">
                        {{ $sub->extracted_transaction_id ?? 'Transaction ID not extracted' }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $sub->created_at->format('d M Y, H:i') }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $sub->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $sub->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $sub->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst($sub->status) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('upload-prompt').classList.add('hidden');
        document.getElementById('preview-area').classList.remove('hidden');
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('preview-name').textContent = file.name;
    };
    reader.readAsDataURL(file);
}

function clearImage() {
    document.getElementById('screenshot').value = '';
    document.getElementById('upload-prompt').classList.remove('hidden');
    document.getElementById('preview-area').classList.add('hidden');
}
</script>
</x-app-layout>