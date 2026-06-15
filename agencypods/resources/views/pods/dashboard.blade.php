@extends('layouts.pods')

@section('title', $currentPod?->name ?? 'Dashboard')

@php
    $sectionLabels = ['goal' => 'Goal', 'stop' => 'Stop', 'start' => 'Start', 'continue' => 'Continue'];
    $iconMap = ['pdf' => 'ti-file-type-pdf', 'img' => 'ti-photo', 'xls' => 'ti-table', 'doc' => 'ti-file'];
@endphp

@section('content')
<div style="--pod: {{ $currentPod?->color ?? '#FCD82F' }}">
@if(! $currentPod)
    <div class="empty-state">
        <i class="ti ti-folder-off" style="font-size:32px;margin-bottom:8px;display:block" aria-hidden="true"></i>
        No pods are assigned to you yet.
    </div>
@else
    {{-- Clients --}}
    <div class="section-header">
        <span class="section-title">Clients</span>
        <button class="btn btn-primary" onclick="document.getElementById('addClientDlg').showModal()">
            <i class="ti ti-plus" aria-hidden="true"></i> Add client
        </button>
    </div>

    @if($clients->isEmpty())
        <div class="empty-state" style="padding:20px">No clients in this pod yet.</div>
    @else
        <div class="client-grid">
            @foreach($clients as $cl)
                <a class="client-card {{ $currentClient && $cl->id === $currentClient->id ? 'selected' : '' }}"
                   href="{{ route('dashboard', ['pod' => $currentPod->id, 'client' => $cl->id]) }}">
                    <div class="client-name">{{ $cl->name }}</div>
                    <div class="client-meta">{{ $cl->industry }}</div>
                    <span class="goal-count"><i class="ti ti-target" style="font-size:11px" aria-hidden="true"></i>
                        {{ $cl->goals->count() }} goal{{ $cl->goals->count() === 1 ? '' : 's' }}</span>
                </a>
            @endforeach
        </div>

        <div class="divider"></div>

        {{-- Goals for the selected client --}}
        <div class="section-header">
            <span class="section-title">Goals — {{ $currentClient->name }}</span>
            <div style="display:flex;gap:6px">
                <button class="btn" onclick="editClient({{ $currentClient->id }}, @js($currentClient->name), @js($currentClient->industry))">
                    <i class="ti ti-pencil" aria-hidden="true"></i> Edit client
                </button>
                <form method="POST" action="{{ route('clients.destroy', $currentClient) }}"
                      onsubmit="return confirm('Delete {{ $currentClient->name }} and all its goals?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger" type="submit"><i class="ti ti-trash" aria-hidden="true"></i></button>
                </form>
                <button class="btn btn-primary" onclick="document.getElementById('addGoalDlg').showModal()">
                    <i class="ti ti-plus" aria-hidden="true"></i> Add goal
                </button>
            </div>
        </div>

        @if($currentClient->goals->isEmpty())
            <div class="empty-state" style="padding:20px">No goals yet for this client.</div>
        @else
            @foreach($currentClient->goals as $goal)
                @php $isOpen = $goal->id === $openGoalId; @endphp
                <details class="goal-card" {{ $isOpen ? 'open' : '' }} id="goal-{{ $goal->id }}">
                    <summary>
                        <div>
                            <div class="goal-title">{{ $goal->title }}</div>
                            <div class="goal-subtitle">Created {{ $goal->created_at->format('M Y') }}</div>
                        </div>
                        <i class="ti ti-chevron-down chevron" aria-hidden="true"></i>
                    </summary>

                    @php $active = $isOpen ? $openTab : 'goal'; @endphp
                    <div class="tabs" data-goal="{{ $goal->id }}">
                        @foreach(\App\Models\Goal::SECTION_TYPES as $type)
                            <button class="tab {{ $active === $type ? 'active' : '' }}"
                                    data-tab-btn="{{ $type }}" type="button">{{ $sectionLabels[$type] }}</button>
                        @endforeach
                    </div>

                    @foreach(\App\Models\Goal::SECTION_TYPES as $type)
                        @php $section = $goal->sections->firstWhere('type', $type); @endphp
                        <div class="tab-body" data-goal="{{ $goal->id }}" data-tab="{{ $type }}" {{ $active === $type ? '' : 'hidden' }}>
                            {{-- View / edit text --}}
                            <div data-text-view>
                                <div class="tab-text {{ $section->content ? '' : 'empty' }}">{{ $section->content ?: 'No notes yet.' }}</div>
                                <div class="tab-toolbar">
                                    <button class="btn" type="button" onclick="toggleEdit(this)"><i class="ti ti-pencil" aria-hidden="true"></i> Edit text</button>
                                </div>
                            </div>
                            <form data-text-edit hidden method="POST" action="{{ route('sections.update', $section) }}">
                                @csrf @method('PUT')
                                <div class="field">
                                    <textarea name="content" placeholder="Write the {{ $sectionLabels[$type] }} notes…">{{ $section->content }}</textarea>
                                </div>
                                <div class="tab-toolbar">
                                    <button class="btn btn-primary" type="submit"><i class="ti ti-check" aria-hidden="true"></i> Save</button>
                                    <button class="btn" type="button" onclick="toggleEdit(this)">Cancel</button>
                                </div>
                            </form>

                            {{-- Attachments (real uploads land in Phase 4) --}}
                            <div class="attachments-label">
                                <i class="ti ti-paperclip" style="font-size:12px;vertical-align:-1px;margin-right:4px" aria-hidden="true"></i>
                                Attachments {{ $section->attachments->count() ? '('.$section->attachments->count().')' : '' }}
                            </div>
                            @if($section->attachments->count())
                                <div class="file-list">
                                    @foreach($section->attachments as $file)
                                        <div class="file-item">
                                            <div class="file-left">
                                                <div class="file-icon {{ $file->file_type }}"><i class="ti {{ $iconMap[$file->file_type] ?? 'ti-file' }}" aria-hidden="true"></i></div>
                                                <div>
                                                    <div class="file-name">{{ $file->original_name }}</div>
                                                    <div class="file-size">{{ $file->human_size }}{{ $file->stored_path ? '' : ' · sample' }}</div>
                                                </div>
                                            </div>
                                            <div class="file-actions">
                                                @if($file->stored_path)
                                                    <a class="icon-btn" href="{{ route('attachments.preview', $file) }}" target="_blank" title="Preview"><i class="ti ti-eye" aria-hidden="true"></i></a>
                                                    <a class="icon-btn" href="{{ route('attachments.download', $file) }}" title="Download"><i class="ti ti-download" aria-hidden="true"></i></a>
                                                @endif
                                                <form method="POST" action="{{ route('attachments.destroy', $file) }}" onsubmit="return confirm('Remove {{ $file->original_name }}?')">
                                                    @csrf @method('DELETE')
                                                    <button class="icon-btn" type="submit" title="Remove"><i class="ti ti-trash" aria-hidden="true"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <form method="POST" action="{{ route('attachments.store', $section) }}" enctype="multipart/form-data">
                                @csrf
                                <label class="upload-zone">
                                    <i class="ti ti-cloud-upload" style="font-size:18px;display:block;margin-bottom:4px" aria-hidden="true"></i>
                                    Click to upload a file
                                    <input type="file" name="file" required hidden onchange="this.form.submit()">
                                </label>
                            </form>
                        </div>
                    @endforeach

                    <div class="tab-body" style="border-top:0.5px solid var(--color-border-tertiary);display:flex;gap:6px">
                        <button class="btn" onclick="editGoal({{ $goal->id }}, @js($goal->title))"><i class="ti ti-pencil" aria-hidden="true"></i> Rename goal</button>
                        <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger" type="submit"><i class="ti ti-trash" aria-hidden="true"></i> Delete goal</button>
                        </form>
                    </div>
                </details>
            @endforeach
        @endif
    @endif

    {{-- ===== Dialogs ===== --}}
    <dialog id="addClientDlg">
        <form method="POST" action="{{ route('clients.store') }}" class="dlg-body">
            @csrf
            <input type="hidden" name="pod_id" value="{{ $currentPod->id }}">
            <div class="dlg-title">Add client to {{ $currentPod->name }}</div>
            <div class="field"><label>Name</label><input name="name" required autofocus></div>
            <div class="field"><label>Industry</label><input name="industry" placeholder="e.g. F&amp;B, Legal"></div>
            <div class="dlg-actions">
                <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
                <button class="btn btn-primary" type="submit">Add client</button>
            </div>
        </form>
    </dialog>

    <dialog id="editClientDlg">
        <form method="POST" id="editClientForm" class="dlg-body">
            @csrf @method('PUT')
            <div class="dlg-title">Edit client</div>
            <div class="field"><label>Name</label><input name="name" id="editClientName" required></div>
            <div class="field"><label>Industry</label><input name="industry" id="editClientIndustry"></div>
            <div class="dlg-actions">
                <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </dialog>

    @if($currentClient)
    <dialog id="addGoalDlg">
        <form method="POST" action="{{ route('goals.store') }}" class="dlg-body">
            @csrf
            <input type="hidden" name="client_id" value="{{ $currentClient->id }}">
            <div class="dlg-title">Add goal for {{ $currentClient->name }}</div>
            <div class="field"><label>Title</label><input name="title" required autofocus placeholder="e.g. Website redesign"></div>
            <div class="dlg-actions">
                <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
                <button class="btn btn-primary" type="submit">Add goal</button>
            </div>
        </form>
    </dialog>
    @endif

    <dialog id="editGoalDlg">
        <form method="POST" id="editGoalForm" class="dlg-body">
            @csrf @method('PUT')
            <div class="dlg-title">Rename goal</div>
            <div class="field"><label>Title</label><input name="title" id="editGoalTitle" required></div>
            <div class="dlg-actions">
                <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </dialog>
@endif
</div>
@endsection

@push('scripts')
<script>
// Tab switching within an open goal (pure UI, no reload).
document.querySelectorAll('.tabs').forEach(function (tabs) {
    var goalId = tabs.dataset.goal;
    tabs.querySelectorAll('[data-tab-btn]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var type = btn.dataset.tabBtn;
            tabs.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('.tab-body[data-goal="' + goalId + '"]').forEach(function (body) {
                body.hidden = body.dataset.tab !== type;
            });
        });
    });
});

// Toggle a section's text view <-> edit form.
function toggleEdit(el) {
    var body = el.closest('.tab-body');
    var view = body.querySelector('[data-text-view]');
    var edit = body.querySelector('[data-text-edit]');
    var showEdit = view.hidden === false;
    view.hidden = showEdit;
    edit.hidden = !showEdit;
}

// Edit-client dialog.
function editClient(id, name, industry) {
    var f = document.getElementById('editClientForm');
    f.action = '{{ url('clients') }}/' + id;
    document.getElementById('editClientName').value = name;
    document.getElementById('editClientIndustry').value = industry || '';
    document.getElementById('editClientDlg').showModal();
}

// Edit-goal dialog.
function editGoal(id, title) {
    var f = document.getElementById('editGoalForm');
    f.action = '{{ url('goals') }}/' + id;
    document.getElementById('editGoalTitle').value = title;
    document.getElementById('editGoalDlg').showModal();
}
</script>
@endpush
