{{-- resources/views/usermenu/submenu/create.blade.php --}}
@extends('adminlte::page')
@section('title', 'Create Submenu')


@section('content')
<div class="container">
    <h4 class="mb-3">Create Submenu</h4>

    <form method="POST" action="{{ route('usermenu.storeSubmenu') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Parent Menu</label>
            <select id="menuparentcode" name="menuparentcode" class="form-select" required>
                <option value="">-- Select Parent Menu --</option>
                @foreach($parents as $p)
                    <option value="{{ $p->code }}">{{ $p->name }} ({{ $p->code }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Next Submenu Code</label>
            <input type="number" id="submenucode" name="submenucode"
                   class="form-control" readonly placeholder="Will auto load..." />
        </div>

        <div class="mb-3">
            <label class="form-label">Submenu Name</label>
            <input type="text" name="submenuname" class="form-control" required />
        </div>

        <div class="mb-3">
            <label class="form-label">Method</label>
            <input type="text" name="method" class="form-control" required />
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="number" name="position" class="form-control" required />
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>

{{-- === JavaScript Section === --}}
<script>
document.getElementById('menuparentcode').addEventListener('change', async function () {
    const parent = this.value;
    const codeInput = document.getElementById('submenucode');
    codeInput.value = ''; // clear old value

    if (!parent) return; // if not selected

    try {
        const response = await fetch(`/usermenu/next-code/${parent}`);
        const data = await response.json();

        if (data.ok) {
            codeInput.value = data.next_code;
        } else {
            codeInput.value = 'Error';
        }
    } catch (error) {
        console.error(error);
        codeInput.value = 'Error loading';
    }
});
</script>
@endsection
