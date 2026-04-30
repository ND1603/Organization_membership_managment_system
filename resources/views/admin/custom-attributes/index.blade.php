<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4 fw-bold">Custom Member Attributes</h2>

        {{-- Success message --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">

            {{-- LEFT SIDE — Add New Attribute Form --}}
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Add New Attribute</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.custom-attributes.store') }}">
                            @csrf

                            {{-- Internal name --}}
                            <div class="mb-2">
                                <label class="form-label">
                                    Internal Name
                                    <small class="text-muted">(no spaces, e.g. department)</small>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="e.g. department"
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Display label --}}
                            <div class="mb-2">
                                <label class="form-label">Display Label</label>
                                <input
                                    type="text"
                                    name="label"
                                    value="{{ old('label') }}"
                                    class="form-control @error('label') is-invalid @enderror"
                                    placeholder="e.g. Department"
                                >
                                @error('label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Field type --}}
                            <div class="mb-2">
                                <label class="form-label">Field Type</label>
                                <select name="type" class="form-select" id="fieldType">
                                    <option value="text">Text</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="select">Dropdown (Select)</option>
                                    <option value="boolean">Yes / No</option>
                                </select>
                            </div>

                            {{-- Options — only shown when type is select --}}
                            <div class="mb-2" id="optionsField" style="display:none">
                                <label class="form-label">
                                    Options
                                    <small class="text-muted">(comma-separated)</small>
                                </label>
                                <input
                                    type="text"
                                    name="options"
                                    class="form-control"
                                    placeholder="e.g. IT, Finance, HR"
                                >
                            </div>

                            {{-- Sort order --}}
                            <div class="mb-2">
                                <label class="form-label">
                                    Sort Order
                                    <small class="text-muted">(lower = appears first)</small>
                                </label>
                                <input
                                    type="number"
                                    name="sort_order"
                                    value="{{ old('sort_order', 0) }}"
                                    class="form-control"
                                >
                            </div>

                            {{-- Required toggle --}}
                            <div class="mb-3 form-check">
                                <input
                                    type="checkbox"
                                    name="is_required"
                                    value="1"
                                    class="form-check-input"
                                    id="isRequired"
                                >
                                <label class="form-check-label" for="isRequired">
                                    Required field
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Add Attribute
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE — Existing Attributes Table --}}
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Existing Attributes</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Label</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Required</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attributes as $attr)
                                <tr>
                                    <td>{{ $attr->label }}</td>
                                    <td><code>{{ $attr->name }}</code></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $attr->type }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $attr->is_required ? '✓ Yes' : '— No' }}
                                    </td>
                                    <td>
                                        @if($attr->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            {{-- Toggle active/inactive --}}
                                            <form method="POST"
                                                action="{{ route('admin.custom-attributes.toggle', $attr) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary">
                                                    {{ $attr->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>

                                            {{-- Delete --}}
                                            <form method="POST"
                                                action="{{ route('admin.custom-attributes.destroy', $attr) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this attribute and ALL member values for it?')"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No custom attributes defined yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Show options field only when type = select --}}
    <script>
        document.getElementById('fieldType').addEventListener('change', function () {
            const optionsField = document.getElementById('optionsField');
            optionsField.style.display = this.value === 'select' ? 'block' : 'none';
        });
    </script>

</x-app-layout>