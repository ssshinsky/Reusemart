@extends('Organisasi.dashboard')

@section('title', 'Edit Donation Request')

@section('content')
<h2 style="margin-bottom: 1.5rem;">Edit Donation Request</h2>

<form action="{{ route('organisasi.request.update', $requestDonasi->id_request) }}" method="POST" class="form-container">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="request">Request</label>
        <textarea name="request" id="request" rows="5" required>{{ old('request', $requestDonasi->request) }}</textarea>
        @error('request') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <div class="form-actions">
        <a href="{{ route('organisasi.index') }}" class="btn btn-cancel">Cancel</a>
        <button type="submit" class="btn btn-submit">Update</button>
    </div>
</form>

<style>
    .form-container {
        width: 100%;
        padding: 2rem;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .form-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
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

    .form-group input,
    .form-group textarea {
        padding: 0.6rem;
        font-family: inherit;
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

    .btn-reset {
        background-color: #e53e3e;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .btn:hover, .btn-reset:hover {
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
