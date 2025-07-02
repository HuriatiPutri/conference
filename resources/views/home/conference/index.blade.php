@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <div class="content-wrapper" style="min-height: 80vh; margin-left: 0;">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Daftar Konferensi</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Konferensi</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header card-header-main">
                                    <h3 class="card-title">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Data Konferensi Tersedia
                                    </h3>
                                    <div class="card-tools">
                                        <a href="{{ route('conference.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Tambah Konferensi
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="conferencesTable" class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Inisial</th>
                                                    <th>Tanggal</th>
                                                    <th>Poster</th>
                                                    <th>Kota</th>
                                                    <th>Negara</th>
                                                    <th>Tahun</th>
                                                    <th class="text-end">Biaya Online</th>
                                                    <th class="text-end">Biaya Onsite</th>
                                                    <th class="text-end">Biaya Partisipan</th>
                                                    <th class="text-center">Link Registrasi</th>
                                                    <th style="width: 150px" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Loop melalui data konferensi --}}
                                                <?php $no = 1 ?>
                                                @forelse($conferences as $conference)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $conference->name }}</td>
                                                    <td>{{ $conference->initial ?? '-' }}</td> {{-- Tampilkan inisial, jika kosong tampilkan '-' --}}
                                                    <td>{{ $conference->date ? \Carbon\Carbon::parse($conference->date)->format('d/m/Y') : '-' }}</td> {{-- Format tanggal --}}
                                                    <td>
                                                        @if($conference->cover_poster_path)
                                                        <img src="{{ Storage::url($conference->cover_poster_path) }}" alt="Poster {{ $conference->name }}" class="cover-thumb img-thumbnail">
                                                        @else
                                                        -
                                                        @endif
                                                    </td>
                                                    <td>{{ $conference->city }}</td>
                                                    <td>{{ $conference->country }}</td>
                                                    <td>{{ $conference->year }}</td>
                                                    <td class="text-end">Rp {{ number_format($conference->online_fee, 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp {{ number_format($conference->onsite_fee, 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp {{ number_format($conference->participant_fee, 0, ',', '.') }}</td>
                                                    <td class="text-center">
                                                        @php
                                                        $registrationUrl = route('registration.create', $conference->id);
                                                        @endphp
                                                        <i class="fas fa-copy copy-button"
                                                            data-url="{{ $registrationUrl }}"
                                                            title="Salin Link Registrasi"></i>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('conference.show', $conference->id) }}" class="btn btn-info btn-sm" title="Lihat"><i class="fas fa-eye"></i></a>
                                                        <a href="{{ route('conference.edit', $conference->id) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                                                        <form action="{{ route('conference.destroy', $conference->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus konferensi ini?')"><i class="fas fa-trash"></i></button>
                                                        </form>

                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="12" class="text-center">Belum ada data konferensi yang tersedia.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer clearfix">
                                    {{-- DataTables akan menangani pagination secara otomatis --}}
                                    <small class="float-right text-muted">Total: {{ $conferences->count() }} konferensi</small>
                                </div>
                            </div>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <style>
        .card-header-main {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            background-color: #f8f9fa;
        }

        /* Penyesuaian untuk tampilan DataTables di dalam card body tanpa padding */
        .card-body .dataTables_wrapper .row {
            padding: 1rem;
            /* Tambahkan padding agar kontrol DataTables tidak mepet */
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px;
        }

        .table img.cover-thumb {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
            object-fit: cover;
        }

        .copy-button {
            cursor: pointer;
            color: #007bff;
            /* Warna biru Bootstrap */
            transition: color 0.2s ease-in-out;
        }

        .copy-button:hover {
            color: #0056b3;
            /* Warna biru lebih gelap saat hover */
        }

        .copy-button.copied {
            color: #28a745;
            /* Warna hijau saat berhasil disalin */
        }
    </style>
    @stop

    @section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#conferencesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
                },
                "responsive": true,
                "columnDefs": [{
                        "orderable": false,
                        "targets": [4, 11, 12]
                    }, // Non-aktifkan sorting untuk Poster, Link Registrasi, dan Aksi
                    {
                        "searchable": false,
                        "targets": [4, 11, 12]
                    } // Non-aktifkan pencarian untuk Poster, Link Registrasi, dan Aksi
                ]
            });

            // Logika Copy to Clipboard
            $(document).on('click', '.copy-button', function() {
                var urlToCopy = $(this).data('url');
                var $this = $(this); // Simpan referensi ke tombol yang diklik

                // Menggunakan Clipboard API modern
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(urlToCopy).then(function() {
                        // Berhasil disalin
                        $this.addClass('copied');
                        $this.removeClass('fa-copy').addClass('fa-check');
                        $this.attr('title', 'Link Disalin!');

                        setTimeout(function() {
                            $this.removeClass('copied fa-check').addClass('fa-copy');
                            $this.attr('title', 'Salin Link Registrasi');
                        }, 2000); // Kembali ke ikon dan warna asli setelah 2 detik
                    }).catch(function(err) {
                        // Gagal menyalin
                        alert('Gagal menyalin link: ' + err);
                        console.error('Could not copy text: ', err);
                    });
                } else {
                    // Fallback untuk browser lama
                    var textArea = document.createElement("textarea");
                    textArea.value = urlToCopy;
                    textArea.style.position = "fixed"; // Untuk menghindari scroll ke bawah
                    textArea.style.top = "0";
                    textArea.style.left = "0";
                    textArea.style.width = "2em";
                    textArea.style.height = "2em";
                    textArea.style.padding = "0";
                    textArea.style.border = "none";
                    textArea.style.outline = "none";
                    textArea.style.boxShadow = "none";
                    textArea.style.background = "transparent";
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        var successful = document.execCommand('copy');
                        var msg = successful ? 'Berhasil disalin!' : 'Gagal menyalin!';
                        alert(msg); // Bisa diganti dengan tooltip atau notifikasi
                    } catch (err) {
                        alert('Gagal menyalin link (fallback): ' + err);
                        console.error('Fallback: Oops, unable to copy', err);
                    }
                    document.body.removeChild(textArea);
                }
            });
        });
    </script>
    @stop