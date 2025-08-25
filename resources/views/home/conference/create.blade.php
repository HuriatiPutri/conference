@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
  <h1>Add New Conference</h1>
@stop

@section('content')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-8 offset-md-2">
          <div class="card card-warning card-outline">
            <div class="card-header card-header-main">
              <h3 class="card-title">
                <i class="fas fa-edit mr-1"></i>
                Formulir Tambah Data Konferensi
              </h3>
            </div>
            <div class="card-body">
              <form action="{{ route('conference.store') }}" method="POST" **enctype="multipart/form-data" **>
                @csrf {{-- Token CSRF untuk keamanan Laravel --}}

                <div class="mb-3">
                  <label for="name" class="form-label fw-bold">Nama Konferensi <span
                      class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" value="{{ old('name') }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="initial" class="form-label fw-bold">Inisial Konferensi</label>
                  <input type="text" class="form-control @error('initial') is-invalid @enderror" id="initial"
                    name="initial" value="{{ old('initial') }}">
                  <div class="form-text">Contoh: SAFE2024, ICOAS2025</div>
                  @error('initial')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="date" class="form-label fw-bold">Tanggal Konferensi</label>
                  <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                    name="date" value="{{ old('date') }}">
                  <div class="form-text">Pilih tanggal utama konferensi.</div>
                  @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="cover_poster" class="form-label fw-bold">Cover Poster Konferensi</label>
                  <input type="file" class="form-control @error('cover_poster') is-invalid @enderror" id="cover_poster"
                    name="cover_poster" accept="image/*">
                  <div class="form-text">Unggah gambar poster (JPG, PNG, GIF, dll.). Maksimal 2MB.</div>
                  @error('cover_poster')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="city" class="form-label fw-bold">Kota <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                      name="city" value="{{ old('city') }}" required>
                    @error('city')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="country" class="form-label fw-bold">Negara <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country"
                      name="country" value="{{ old('country') }}" required>
                    @error('country')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label for="year" class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                  <input type="number" class="form-control @error('year') is-invalid @enderror" id="year"
                    name="year" value="{{ old('year', date('Y')) }}" required min="{{ date('Y') - 10 }}"
                    max="{{ date('Y') + 10 }}">
                  @error('year')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <hr class="my-4">
                <h4>Biaya Pendaftaran</h4>

                <div class="container">
                  <div class="row">
                    <div class="col-md-6">
                      <span>Partisipan Nasional (IDR)</span>
                      <div class="mb-3">
                        <label for="online_fee" class="form-label fw-bold">Biaya Online <span
                            class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text">Rp</span>
                          <input type="number" step="0.01"
                            class="form-control @error('online_fee') is-invalid @enderror" id="online_fee"
                            name="online_fee" value="{{ old('online_fee', 0) }}" required
                            min="0">
                        </div>
                        @error('online_fee')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="mb-3">
                        <label for="onsite_fee" class="form-label fw-bold">Biaya Onsite <span
                            class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text">Rp</span>
                          <input type="number" step="0.01"
                            class="form-control @error('onsite_fee') is-invalid @enderror" id="onsite_fee"
                            name="onsite_fee" value="{{ old('onsite_fee', 0) }}" required
                            min="0">
                        </div>
                        @error('onsite_fee')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="mb-3">
                        <label for="participant_fee" class="form-label fw-bold">Biaya Partisipan Saja <span
                            class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text">Rp</span>
                          <input type="number" step="0.01"
                            class="form-control @error('participant_fee') is-invalid @enderror" id="participant_fee"
                            name="participant_fee" value="{{ old('participant_fee', 0) }}"
                            required min="0">
                        </div>
                        @error('participant_fee')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                    <div class="col-md-6">
                      <span>Partisipan Internasional (USD)</span>
                      <div class="mb-3">
                        <label for="online_fee_usd" class="form-label fw-bold">Biaya Online <span
                            class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text">Rp</span>
                          <input type="number" step="0.01"
                            class="form-control @error('online_fee_usd') is-invalid @enderror" id="online_fee_usd"
                            name="online_fee_usd" value="{{ old('online_fee_usd', 0) }}" required
                            min="0">
                        </div>
                        @error('online_fee_usd')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="mb-3">
                        <label for="onsite_fee_usd" class="form-label fw-bold">Biaya Onsite <span
                            class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text">Rp</span>
                          <input type="number" step="0.01"
                            class="form-control @error('onsite_fee_usd') is-invalid @enderror" id="onsite_fee_usd"
                            name="onsite_fee_usd" value="{{ old('onsite_fee_usd', 0) }}" required
                            min="0">
                        </div>
                        @error('onsite_fee_usd')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="mb-3">
                        <label for="participant_fee_usd" class="form-label fw-bold">Biaya Partisipan Saja <span
                            class="text-danger">*</span></label>
                        <div class="input-group">
                          <span class="input-group-text">Rp</span>
                          <input type="number" step="0.01"
                            class="form-control @error('participant_fee_usd') is-invalid @enderror" id="participant_fee_usd"
                            name="participant_fee_usd" value="{{ old('participant_fee_usd', 0) }}"
                            required min="0">
                        </div>
                        @error('participant_fee_usd')
                          <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>
                </div>
                
                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <label for="online_fee" class="form-label fw-bold"> Rooms <span class="text-danger">*</span></label>
                  <button type="button" class="btn btn-primary btn-sm" onclick="addRoom()">+ Add Room</button>
                </div>
                <div id="rooms-wrapper" class="mb-3">
                  <div class="room-group input-group mb-2">
                    <input type="text" class="form-control" name="room[0][room_name]" placeholder="Room Name"
                      required>
                    <button type="button" class="btn btn-danger" onclick="removeRoom(this)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                  <i class="fas fa-save mr-1"></i> Simpan Konferensi
                </button>
                <a href="{{ route('conference.index') }}" class="btn btn-secondary mt-3">
                  <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop

@section('css')
  {{-- Add here extra stylesheets --}}
  {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
  <script>
    let index = 1;

    function addRoom() {
      const wrapper = document.getElementById('rooms-wrapper');
      const div = document.createElement('div');
      div.classList.add('room-group', 'input-group', 'mb-2');
      div.innerHTML = `
                <input type="text" class="form-control" name="room[${index}][room_name]" placeholder="Room Name" required>
                <button type="button" class="btn btn-danger" onclick="removeRoom(this)">
                    <i class="fas fa-trash"></i>
                </button>
            `;
      wrapper.appendChild(div);
      index++;
    }


    function removeRoom(button) {
      button.parentElement.remove();
    }
  </script>
@stop
