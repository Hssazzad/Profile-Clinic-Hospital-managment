@extends('adminlte::page')


@section('title', 'Assign Menus to User')

@section('content')
<div class="container py-4">

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-3 align-items-end">
        <div class="col-md-6">
          <label for="user_id" class="form-label">Select User</label>
          <select id="user_id" class="form-select">
            <option value="">-- Choose User --</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6 text-end">
          <a id="btnShow" class="btn btn-primary disabled" href="#">Show</a>
        </div>
      </div>
    </div>
  </div>

  <div class="alert alert-info">Pick a user and click <b>Show</b> to see assigned menus.</div>
</div>

@endsection

@push('js')
<script>
  const userSelect = document.getElementById('user_id');
  const btnShow    = document.getElementById('btnShow');

  userSelect.addEventListener('change', () => {
    const uid = userSelect.value;
    if (uid) {
      btnShow.classList.remove('disabled');
      btnShow.href = "{{ route('usermenu.show') }}" + "?user_id=" + encodeURIComponent(uid);
    } else {
      btnShow.classList.add('disabled');
      btnShow.href = "#";
    }
  });
</script>
@endpush
