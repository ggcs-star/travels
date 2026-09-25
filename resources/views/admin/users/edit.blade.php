@extends('admin.layouts.app')

@section('title', 'Edit '.$user->name)

@section('content')

<div class="admin-page">

    <div class="admin-page__header">

        <div class="admin-page__actions">
            <a href="{{ route('admin.users.show', $user) }}" class="admin-button admin-button--primary">
                ← Back to User
            </a>
        </div>

    </div>


    @if($errors->any())
        <div class="admin-alert admin-alert--danger">
            <strong>Please correct the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="admin-card">

            <div class="admin-card__header">
                <div>
                    <span class="admin-eyebrow">USER MANAGEMENT</span>
                    <h2>Edit {{ $user->name }}</h2>
                </div>
            </div>

            <div class="admin-form-grid">

                <div class="admin-form-group">
                    <label for="name">Full name <span>*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        maxlength="150"
                        required
                    >
                    @error('name')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label for="username">Username <span>*</span></label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username', $user->username) }}"
                        maxlength="100"
                        required
                    >
                    @error('username')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label for="email">Email <span>*</span></label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        maxlength="255"
                        required
                    >
                    @error('email')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label for="role">Role <span>*</span></label>
                    <select id="role" name="role" required>
                        <option value="user" @selected(old('role', $user->role) === 'user')>User</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                    @error('role')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="admin-form-group">
                    <label for="status">Status <span>*</span></label>
                    <select id="status" name="status" required>
                        <option value="1" @selected((string) old('status', (int) $user->status) === '1')>Active</option>
                        <option value="0" @selected((string) old('status', (int) $user->status) === '0')>Inactive</option>
                    </select>
                    @error('status')
                        <small class="admin-form-error">{{ $message }}</small>
                    @enderror
                </div>

            </div>

        </div>


        <div class="admin-card">
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                <a href="{{ route('admin.users.show', $user) }}" class="admin-button">Cancel</a>
                <button type="submit" class="admin-button admin-button--primary">Save Changes</button>
            </div>
        </div>

    </form>

</div>

@endsection
