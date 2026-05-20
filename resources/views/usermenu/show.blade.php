@extends('adminlte::page')

@section('title', 'User Menu View')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
      Menus for:
      @if($user)
        <span class="text-primary">{{ $user->name }}</span>
        <small class="text-muted">({{ $user->email }})</small>
      @else
        <span class="text-danger">Unknown User (ID: {{ $userpin }})</span>
      @endif
    </h4>
    <a href="{{ route('usermenu.assign') }}" class="btn btn-outline-secondary">← Back</a>
  </div>

  @csrf {{-- for JS --}}
  <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
  <input type="hidden" id="userpin" value="{{ $userpin }}">

  @forelse($parents as $p)
    <div class="card mb-3" data-parent="{{ $p->parentcode }}">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <strong>{{ $p->parentname }}</strong>
          <small class="text-muted">({{ $p->controllername }} / {{ $p->mainroute }})</small>
        </div>

        <div class="d-flex align-items-center gap-3">
          <span class="badge parent-badge {{ $p->has_parent ? 'bg-success' : 'bg-secondary' }}">
            {{ $p->has_parent ? 'Parent Assigned' : 'Parent Not Assigned' }}
          </span>

          {{-- Parent toggle --}}
          <div class="form-check form-switch">
            <input class="form-check-input toggle-parent"
                   type="checkbox"
                   role="switch"
                   data-parent="{{ $p->parentcode }}"
                   {{ $p->has_parent ? 'checked' : '' }}>
            <label class="form-check-label">On/Off</label>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        @php
          $subs = $submenusByParent[$p->parentcode] ?? collect();
        @endphp

        @if($subs->isEmpty())
          <div class="p-3 text-muted">No submenus found for this parent.</div>
        @else
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 140px;">Sub Code</th>
                  <th>Submenu Name</th>
                  <th style="width: 160px;">Method</th>
                  <th style="width: 100px;" class="text-center">Position</th>
                  <th style="width: 220px;">Assign</th>
                </tr>
              </thead>
              <tbody>
                @foreach($subs as $s)
                  <tr data-parent="{{ $p->parentcode }}" data-sub="{{ $s->submenucode }}">
                    <td><code>{{ $s->submenucode }}</code></td>
                    <td>{{ $s->submenuname }}</td>
                    <td><span class="text-muted">{{ $s->method }}</span></td>
                    <td class="text-center">{{ $s->position }}</td>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <span class="badge sub-badge {{ $s->has_sub ? 'bg-success' : 'bg-secondary' }}">
                          {{ $s->has_sub ? 'Submenu Assigned' : 'Not Assigned' }}
                        </span>

                        {{-- Submenu toggle --}}
                        <div class="form-check form-switch m-0">
                          <input class="form-check-input toggle-sub"
                                 type="checkbox"
                                 role="switch"
                                 data-parent="{{ $p->parentcode }}"
                                 data-sub="{{ $s->submenucode }}"
                                 {{ $s->has_sub ? 'checked' : '' }}>
                          <label class="form-check-label">On/Off</label>
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  @empty
    <div class="alert alert-warning">No parent menus found.</div>
  @endforelse
</div>
<form action="{{ route('system.clear') }}" method="POST">
    @csrf
    <button class="btn btn-danger">Clear All Caches</button>
</form>
@endsection

@push('js')
<script>
(function(){
  const token  = document.getElementById('csrf_token').value;
  const userpin= document.getElementById('userpin').value;

  async function postJSON(url, payload) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': token,
        'Accept':'application/json'
      },
      body: JSON.stringify(payload)
    });
    return res.json();
  }

  // Parent toggles
  document.querySelectorAll('.toggle-parent').forEach(sw => {
    sw.addEventListener('change', async (e) => {
      const box = e.target;
      const parent = box.dataset.parent;
      const on = box.checked;
      const card = box.closest('.card');
      const badge = card.querySelector('.parent-badge');

      // optimistic UI
      badge.classList.toggle('bg-success', on);
      badge.classList.toggle('bg-secondary', !on);
      badge.textContent = on ? 'Parent Assigned' : 'Parent Not Assigned';

      try {
        const resp = await postJSON("{{ route('usermenu.toggleParent') }}", { userpin, menuparentcode: parent, on });
        if (!resp.ok) throw new Error(resp.message || 'Failed');

      } catch (err) {
        // revert UI on error
        box.checked = !on;
        badge.classList.toggle('bg-success', !on);
        badge.classList.toggle('bg-secondary', on);
        badge.textContent = !on ? 'Parent Assigned' : 'Parent Not Assigned';
        alert('❌ Error: ' + err.message);
      }
    });
  });

  // Submenu toggles
  document.querySelectorAll('.toggle-sub').forEach(sw => {
    sw.addEventListener('change', async (e) => {
      const box = e.target;
      const on  = box.checked;
      const tr  = box.closest('tr');
      const parent = tr.dataset.parent;
      const sub    = tr.dataset.sub;

      const subBadge    = tr.querySelector('.sub-badge');
      const parentCard  = box.closest('.card');
      const parentBadge = parentCard.querySelector('.parent-badge');

      // optimistic UI
      subBadge.classList.toggle('bg-success', on);
      subBadge.classList.toggle('bg-secondary', !on);
      subBadge.textContent = on ? 'Submenu Assigned' : 'Not Assigned';

      try {
        const resp = await postJSON("{{ route('usermenu.toggleSub') }}", {
          userpin, menuparentcode: parent, submenucode: sub, on
        });
        if (!resp.ok) throw new Error(resp.message || 'Failed');

        // If sub got added and parent wasn’t assigned, server ensured parent exists; reflect badge
        if (resp.parentHasAnySub) {
          parentBadge.classList.remove('bg-secondary');
          parentBadge.classList.add('bg-success');
          parentBadge.textContent = 'Parent Assigned';
        } else {
          // If no subs remain, you may want to keep parent as assigned or turn it off.
          // Current server code keeps parent row unless you turned it off manually.
          // Uncomment to auto-gray parent when last sub removed:
          // parentBadge.classList.remove('bg-success');
          // parentBadge.classList.add('bg-secondary');
          // parentBadge.textContent = 'Parent Not Assigned';
        }

      } catch (err) {
        // revert UI on error
        box.checked = !on;
        subBadge.classList.toggle('bg-success', !on);
        subBadge.classList.toggle('bg-secondary', on);
        subBadge.textContent = !on ? 'Submenu Assigned' : 'Not Assigned';
        alert('❌ Error: ' + err.message);
      }
    });
  });
})();
</script>
@endpush
