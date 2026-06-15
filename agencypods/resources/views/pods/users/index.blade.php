@extends('layouts.pods')

@section('title', 'Users')

@php $managers = $users->where('role', 'pod_manager'); @endphp

@push('head')
<style>
.check-list{display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:150px;overflow-y:auto;border:1px solid var(--border-strong);border-radius:var(--r-sm);padding:8px}
.check-list label{display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--t1);padding:4px 6px;border-radius:6px;cursor:pointer;text-transform:none;letter-spacing:0;font-weight:400;margin:0}
.check-list label:hover{background:#f6f6f8}
.check-list input{width:auto}
.role-tag{font-size:10.5px;font-weight:600;padding:3px 8px;border-radius:20px;text-transform:uppercase;letter-spacing:.03em}
.role-super{background:var(--ink);color:var(--brand)}
.role-manager{background:var(--brand-soft);color:#7a6300}
.role-team{background:#E4EFFB;color:#185FA5}
.pill-list{display:flex;flex-wrap:wrap;gap:4px}
.pod-chip{display:inline-flex;align-items:center;gap:5px;font-size:11px;background:#f4f4f6;border:1px solid var(--border);padding:2px 8px;border-radius:20px;color:var(--t2)}
.pod-chip .d{width:8px;height:8px;border-radius:50%}
</style>
@endpush

@section('content')
{{-- ===== Users ===== --}}
<div class="section-header">
    <span class="section-title">Users &amp; roles</span>
    <button class="btn btn-primary" onclick="document.getElementById('addUserDlg').showModal()"><i class="ti ti-user-plus" aria-hidden="true"></i> Add user</button>
</div>

<table class="weekly-table" style="margin-bottom:26px">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Pods / Manager</th><th style="text-align:right">Actions</th></tr></thead>
    <tbody>
        @foreach($users as $u)
            <tr>
                <td style="font-weight:600">{{ $u->name }}</td>
                <td style="color:var(--t2)">{{ $u->email }}</td>
                <td>
                    <span class="role-tag {{ $u->isSuperAdmin() ? 'role-super' : ($u->isPodManager() ? 'role-manager' : 'role-team') }}">{{ $u->roleLabel() }}</span>
                </td>
                <td>
                    @if($u->isPodManager())
                        <div class="pill-list">
                            @forelse($u->pods as $p)
                                <span class="pod-chip"><span class="d" style="background:{{ $p->color }}"></span>{{ $p->name }}</span>
                            @empty
                                <span style="color:var(--t3);font-size:12px">No pods assigned</span>
                            @endforelse
                        </div>
                    @elseif($u->isTeamMember())
                        <span style="color:var(--t2);font-size:12px">Reports to {{ optional($u->manager)->name ?? '—' }}</span>
                    @else
                        <span style="color:var(--t3);font-size:12px">All pods</span>
                    @endif
                </td>
                <td>
                    <div style="display:flex;gap:4px;justify-content:flex-end">
                        @if(! $u->isTeamMember())
                            <button class="icon-btn" type="button" title="Edit"
                                onclick="editUser(@js($u->id), @js($u->name), @js($u->email), @js($u->role), @js($u->pods->pluck('id')))"><i class="ti ti-pencil" aria-hidden="true"></i></button>
                        @endif
                        @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Delete {{ $u->name }}?')">
                                @csrf @method('DELETE')
                                <button class="icon-btn" type="submit" title="Delete"><i class="ti ti-trash" aria-hidden="true"></i></button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- ===== Pods ===== --}}
<div class="section-header">
    <span class="section-title">Pods</span>
    <button class="btn btn-primary" onclick="document.getElementById('addPodDlg').showModal()"><i class="ti ti-plus" aria-hidden="true"></i> Add pod</button>
</div>

<table class="weekly-table">
    <thead><tr><th>Pod</th><th>Manager</th><th>Clients</th><th style="text-align:right">Actions</th></tr></thead>
    <tbody>
        @foreach($pods as $p)
            <tr>
                <td style="font-weight:600"><span style="display:inline-block;width:9px;height:9px;border-radius:50%;background:{{ $p->color }};margin-right:7px"></span>{{ $p->name }}</td>
                <td style="color:var(--t2)">{{ optional($p->manager)->name ?? 'Unassigned' }}</td>
                <td>{{ $p->clients()->count() }}</td>
                <td>
                    <div style="display:flex;justify-content:flex-end">
                        <form method="POST" action="{{ route('pods.destroy', $p) }}" onsubmit="return confirm('Delete {{ $p->name }} and all its clients/goals?')">
                            @csrf @method('DELETE')
                            <button class="icon-btn" type="submit" title="Delete pod"><i class="ti ti-trash" aria-hidden="true"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- ===== Dialogs ===== --}}
<dialog id="addUserDlg">
    <form method="POST" action="{{ route('users.store') }}" class="dlg-body">
        @csrf
        <div class="dlg-title">Add user</div>
        <div class="field"><label>Name</label><input name="name" required></div>
        <div class="field"><label>Email</label><input type="email" name="email" required></div>
        <div class="field"><label>Password</label><input type="password" name="password" required minlength="8" placeholder="Min 8 characters"></div>
        <div class="field"><label>Role</label>
            <select name="role" required onchange="togglePods(this, 'addPods')">
                <option value="pod_manager">Pod Manager</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>
        <div class="field" id="addPods"><label>Pods managed</label>
            <div class="check-list">
                @foreach($pods as $p)
                    <label><input type="checkbox" name="pods[]" value="{{ $p->id }}"> {{ $p->name }}</label>
                @endforeach
            </div>
        </div>
        <div class="dlg-actions">
            <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="btn btn-primary" type="submit">Create user</button>
        </div>
    </form>
</dialog>

<dialog id="editUserDlg">
    <form method="POST" id="editUserForm" class="dlg-body">
        @csrf @method('PUT')
        <div class="dlg-title">Edit user</div>
        <div class="field"><label>Name</label><input name="name" id="euName" required></div>
        <div class="field"><label>Email</label><input type="email" name="email" id="euEmail" required></div>
        <div class="field"><label>New password <span style="text-transform:none;color:var(--t3)">(leave blank to keep)</span></label><input type="password" name="password" id="euPassword" minlength="8"></div>
        <div class="field"><label>Role</label>
            <select name="role" id="euRole" required onchange="togglePods(this, 'euPods')">
                <option value="pod_manager">Pod Manager</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>
        <div class="field" id="euPods"><label>Pods managed</label>
            <div class="check-list">
                @foreach($pods as $p)
                    <label><input type="checkbox" name="pods[]" value="{{ $p->id }}" data-pod="{{ $p->id }}"> {{ $p->name }}</label>
                @endforeach
            </div>
        </div>
        <div class="dlg-actions">
            <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
    </form>
</dialog>

<dialog id="addPodDlg">
    <form method="POST" action="{{ route('pods.store') }}" class="dlg-body">
        @csrf
        <div class="dlg-title">Add pod</div>
        <div class="field"><label>Name</label><input name="name" required placeholder="e.g. Pod Epsilon"></div>
        <div class="field"><label>Colour</label><input type="color" name="color" value="#FCD82F" style="height:40px;padding:4px"></div>
        <div class="field"><label>Manager</label>
            <select name="manager_id">
                <option value="">Unassigned</option>
                @foreach($managers as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="dlg-actions">
            <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="btn btn-primary" type="submit">Create pod</button>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
function togglePods(select, containerId) {
    document.getElementById(containerId).style.display = select.value === 'pod_manager' ? '' : 'none';
}

function editUser(id, name, email, role, podIds) {
    var f = document.getElementById('editUserForm');
    f.action = '{{ url('users') }}/' + id;
    document.getElementById('euName').value = name;
    document.getElementById('euEmail').value = email;
    document.getElementById('euPassword').value = '';
    document.getElementById('euRole').value = role;
    document.querySelectorAll('#euPods input[data-pod]').forEach(function (cb) {
        cb.checked = podIds.map(String).includes(cb.dataset.pod);
    });
    togglePods(document.getElementById('euRole'), 'euPods');
    document.getElementById('editUserDlg').showModal();
}
</script>
@endpush
