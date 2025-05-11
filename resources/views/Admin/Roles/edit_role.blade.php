@extends('Admin.admin')

@section('title', 'Edit Role')

@section('content')
<h2 style="margin-bottom: 1.5rem;">Edit Role</h2>

<form action="{{ route('admin.roles.update', $role->id_role) }}" method="POST" class="form-container">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-column">
            <div class="form-group">
                <label for="nama_role">Role Name</label>
                <input type="text" name="nama_role" id="nama_role" value="{{ $role->nama_role }}" required>
            </div>
        </div>
    </div>

    <div class="form-actions-container">
        <div class="form-actions">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-cancel">Cancel</a>
            <button type="submit" class="btn btn-submit">Update</button>
        </div>
    </div>
</form>

<style>
    .form-container {
        width: 50%;
        padding: 2rem;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .form-grid {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .form-column {
        flex: 1;
        min-width: 300px;
    }

    .form-group {
        margin-bottom: 1.2rem;
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 0.4rem;
        font-weight: 600;
        font-size: 16px;
    }

    .form-group input {
        padding: 0.6rem;
        font-size: 16px;
        font-weight: 400;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .form-actions-container {
        margin-top: 2rem;
        display: flex;
        justify-content: flex-end;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
    }

    .btn {
        height: 48px;
        padding: 0 1.5rem;
        font-size: 16px;
        font-weight: 400;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel {
        background-color: #dc3545;
        color: white;
        text-decoration: none;
    }

    .btn-submit {
        background-color: #28a745;
        color: white;
    }

    .btn:hover {
        opacity: 0.95;
    }

    @media (max-width: 768px) {
        .form-grid {
            flex-direction: column;
        }

        .form-actions-container {
            justify-content: center;
        }
    }
</style>
@endsection
