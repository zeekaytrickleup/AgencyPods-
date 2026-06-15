@extends('layouts.pods')

@section('title', 'My team')

@section('content')
<div class="section-header">
    <span class="section-title">My team</span>
    <button class="btn btn-primary" onclick="document.getElementById('addMemberDlg').showModal()"><i class="ti ti-user-plus" aria-hidden="true"></i> Add team member</button>
</div>

<div style="background:var(--brand-soft);border:1px solid #F0DE8E;border-radius:var(--r-sm);padding:11px 14px;font-size:12.5px;color:#7a6300;margin-bottom:16px;display:flex;gap:8px;align-items:center">
    <i class="ti ti-info-circle" aria-hidden="true"></i>
    Team members you add can sign in and work on the clients &amp; goals in <strong>your pods</strong>.
</div>

@if($team->isEmpty())
    <div class="empty-state">You haven’t added any team members yet.</div>
@else
    <table class="weekly-table">
        <thead><tr><th>Name</th><th>Email</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
            @foreach($team as $member)
                <tr>
                    <td style="font-weight:600">{{ $member->name }}</td>
                    <td style="color:var(--t2)">{{ $member->email }}</td>
                    <td>
                        <div style="display:flex;justify-content:flex-end">
                            <form method="POST" action="{{ route('team.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->name }} from your team?')">
                                @csrf @method('DELETE')
                                <button class="icon-btn" type="submit" title="Remove"><i class="ti ti-trash" aria-hidden="true"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<dialog id="addMemberDlg">
    <form method="POST" action="{{ route('team.store') }}" class="dlg-body">
        @csrf
        <div class="dlg-title">Add team member</div>
        <div class="field"><label>Name</label><input name="name" required autofocus></div>
        <div class="field"><label>Email</label><input type="email" name="email" required></div>
        <div class="field"><label>Password</label><input type="password" name="password" required minlength="8" placeholder="Min 8 characters"></div>
        <div class="dlg-actions">
            <button class="btn" type="button" onclick="this.closest('dialog').close()">Cancel</button>
            <button class="btn btn-primary" type="submit">Add member</button>
        </div>
    </form>
</dialog>
@endsection
