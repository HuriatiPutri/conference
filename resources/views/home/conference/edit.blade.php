@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
  <h1>Edit Conference</h1>
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
                Formulir Edit Data Konferensi
              </h3>
            </div>
            <div class="card-body">
              <form action="{{ route('conference.update', $conference->public_id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf {{-- Token CSRF untuk keamanan Laravel --}}
                @method('PUT') {{-- Digunakan untuk mengirimkan permintaan PUT/PATCH --}}

                <div class="mb-3">
                  <label for="name" class="form-label fw-bold">Nama Konferensi <span
                      class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" value="{{ old('name', $conference->name) }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="initial" class="form-label fw-bold">Inisial Konferensi</label>
                  <input type="text" class="form-control @error('initial') is-invalid @enderror" id="initial"
                    name="initial" value="{{ old('initial', $conference->initial) }}">
                  <div class="form-text">Contoh: SAFE2024, ICOAS2025</div>
                  @error('initial')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="date" class="form-label fw-bold">Tanggal Konferensi</label>
                  <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                    name="date" value="{{ old('date', $conference->date ? $conference->date->format('Y-m-d') : '') }}">
                  <div class="form-text">Pilih tanggal utama konferensi.</div>
                  @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="cover_poster" class="form-label fw-bold">Cover Poster Konferensi</label>
                  @if ($conference->cover_poster_path)
                    <div class="mb-2">
                      <img src="{{ Storage::url($conference->cover_poster_path) }}" alt="Current Poster"
                        class="img-thumbnail cover-poster-preview">
                      <small class="text-muted d-block mt-1">Poster saat ini.</small>
                    </div>
                  @endif
                  <input type="file" class="form-control @error('cover_poster') is-invalid @enderror" id="cover_poster"
                    name="cover_poster" accept="image/*">
                  <div class="form-text">Unggah gambar baru untuk mengganti poster yang ada (maksimal 2MB).</div>
                  @error('cover_poster')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror

                  @if ($conference->cover_poster_path)
                    <div class="form-check mt-2">
                      <input class="form-check-input" type="checkbox" name="remove_cover_poster" id="remove_cover_poster"
                        value="1">
                      <label class="form-check-label" for="remove_cover_poster">
                        Hapus poster yang ada tanpa menggantinya
                      </label>
                    </div>
                  @endif
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="city" class="form-label fw-bold">Kota <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city"
                      name="city" value="{{ old('city', $conference->city) }}" required>
                    @error('city')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="country" class="form-label fw-bold">Negara <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country"
                      name="country" value="{{ old('country', $conference->country) }}" required>
                    @error('country')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label for="year" class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                  <input type="number" class="form-control @error('year') is-invalid @enderror" id="year"
                    name="year" value="{{ old('year', $conference->year) }}" required min="{{ date('Y') - 10 }}"
                    max="{{ date('Y') + 10 }}">
                  @error('year')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <hr class="my-4">
                <h4>Biaya Pendaftaran</h4>

                <div class="mb-3">
                  <label for="online_fee" class="form-label fw-bold">Biaya Online <span
                      class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" step="0.01" class="form-control @error('online_fee') is-invalid @enderror"
                      id="online_fee" name="online_fee" value="{{ old('online_fee', $conference->online_fee) }}"
                      required min="0">
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
                    <input type="number" step="0.01" class="form-control @error('onsite_fee') is-invalid @enderror"
                      id="onsite_fee" name="onsite_fee" value="{{ old('onsite_fee', $conference->onsite_fee) }}"
                      required min="0">
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
                      name="participant_fee" value="{{ old('participant_fee', $conference->participant_fee) }}"
                      required min="0">
                  </div>
                  @error('participant_fee')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <label for="online_fee" class="form-label fw-bold"> Rooms <span class="text-danger">*</span></label>
                  <button type="button" class="btn btn-primary btn-sm" onclick="addRoom()">+ Add Room</button>
                </div>
                @if($conference->rooms->isEmpty())
                  @php
                    $index = 0;
                  @endphp
                <div id="rooms-wrapper" class="mb-3">
                    <div class="room-group input-group mb-2">
                      <input type="text" class="form-control" name="room[{{$index}}][room_name]" placeholder="Room Name" required>
                      <button type="button" class="btn btn-danger" onclick="removeRoom(this)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>
                @endif
                @foreach ($conference->rooms as $index => $room)
                <div id="rooms-wrapper" class="mb-3">
                  <div class="room-group input-group mb-2">
                    <input type="text" class="form-control" name="room[{{$index}}][room_name]" placeholder="Room Name"
                    value="{{ old("room.$index.room_name", $room->room_name) }}" required>
                    <button type="button" class="btn btn-danger" onclick="removeRoom(this)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
                @endforeach

                <button type="submit" class="btn btn-warning mt-3">
                  <i class="fas fa-save mr-1"></i> Update Konferensi
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
    let index = {{ $conference->rooms->count() }};

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
