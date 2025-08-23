@extends('layouts.app')
@section('title', 'Download Certificate')

@section('content')
  <main role="main" class="container">
    <div class="container mt-4">
      <div class="card">
        <div class="card-header">
          Download Your Certificate
        </div>
        <div class="row">
          <div class="col-md-8 offset-md-2">
            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
          </div>
          <div class="card-body">
            <form action="{{ route('certificate.store') }}" method="POST"
              enctype="multipart/form-data">
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Conference <span class="text-danger">*</span></label>
                <select class="form-control @error('conference_id') is-invalid @enderror" id="conference_id"
                  name="conference_id" required>
                  <option value="" disabled selected>Select Conference</option>
                  @foreach ($conferences as $conference)
                    <option value="{{ $conference->id }}" {{ old('conference_id') == $conference->id ? 'selected' : '' }}>
                      {{ $conference->name }} ({{ $conference->year }}) - {{ $conference->city }}, {{ $conference->country }}
                    </option>
                  @endforeach
                </select>
                @error('conference_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                  name="email" value="{{ old('email') }}" required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <button type="submit" class="btn btn-primary mt-3">
                Download Certificate
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection
@section('scripts')
@endsection
