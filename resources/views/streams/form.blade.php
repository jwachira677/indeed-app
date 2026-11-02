@extends('layouts.app')

@section('title', isset($stream) ? 'Edit Stream' : 'Create Stream')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('streams.index') }}" class="btn btn-link text-decoration-none me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="fw-bold mb-0">{{ isset($stream) ? 'Edit Stream: ' . $stream->name : 'Create New Stream' }}</h4>
    </div>

    <div class="row">
        <div class="col-md-10 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    
                    <form method="POST" action="{{ isset($stream) ? route('streams.update', $stream->id) : route('streams.store') }}">
                        @csrf
                        @if(isset($stream)) @method('PUT') @endif

                        <div class="mb-3">
                            <label class="form-label">Stream Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $stream->name ?? '') }}" placeholder="e.g. East" required autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Stream Code</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $stream->code ?? '') }}" placeholder="e.g. E (Optional)">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('streams.index') }}" class="btn btn-light border">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i> {{ isset($stream) ? 'Update Stream' : 'Create Stream' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection