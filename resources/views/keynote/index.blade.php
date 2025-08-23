@extends('layouts.app')
@section('title', 'Keynote Session Registration')

@section('content')
  <main role="main" class="container">
    <div class="container mt-4">
      <div class="card">
        <div class="card-header">
          Keynote Session Registration
          <strong>{{ $conference->name }} ({{ $conference->year }})</strong>
          {{ $conference->city }}, {{ $conference->country }}
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
            <form action="{{ route('keynote.store', $conference->public_id) }}" method="POST"
              enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="conference_id" value="{{ $conference->id }}">
              <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                  name="email" value="{{ old('email') }}" required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                    name="first_name" value="{{ old('first_name') }}" required>
                  @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col-md-6">
                  <label for="last_name" class="form-label">Last Name<span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                    name="last_name" value="{{ old('last_name') }}" required>
                  @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="mb-3">
                <label for="feedback" class="form-label">Feedback <span class="text-danger">*</span></label>
                <textarea type="text" class="form-control @error('feedback') is-invalid @enderror" id="feedback"
                  name="feedback" value="{{ old('feedback') }}" required></textarea>
                @error('feedback')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <button type="submit" class="btn btn-primary mt-3">
                Send
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
