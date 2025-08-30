@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
  <body class="hold-transition sidebar-mini">
    <div class="wrapper">
      <div class="content-wrapper" style="min-height: 80vh; margin-left: 0;">
        <section class="content-header">
          <div class="container-fluid">
            <h1 class="m-0">Daftar Audience</h1>
          </div>
        </section>
        <section class="content">
          <div class="container-fluid">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Data Audience Terdaftar</h3>
                <div class="card-tools"><a href="{{ route('audience.create') }}" class="btn btn-primary btn-sm"><i
                      class="fas fa-plus"></i> Tambah Audience</a></div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                <table id="audiencesTable" class="table-bordered table-hover table-striped table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Konferensi</th>
                      <th>Nama Depan</th>
                      <th>Nama Belakang</th>
                      <th>Email</th>
                      <th>Tipe Partisipan</th>
                      <th>Metode Pembayaran</th>
                      <th>Biaya Dibayar</th>
                      <th>Status Pembayaran</th>
                      <th>Paper</th>
                      <th>Sertifikat</th>
                      <th class="text-center" width="300px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($audiences as $audience)
                      <tr>
                        <td>{{ $audience->id }}</td>
                        <td>{{ $audience->conference->name ?? 'N/A' }}</td>
                        <td>{{ $audience->first_name }}</td>
                        <td>{{ $audience->last_name }}</td>
                        <td>{{ $audience->email }}</td>
                        <td>{{ Str::headline($audience->presentation_type) }}</td>
                        <td>{{ $audience->getPaymentMethodText()}}</td>
                        <td>{{ $audience->country === 'ID' ? 'Rp' : 'USD'}} {{ number_format($audience->paid_fee, 0, ',', '.') }}</td>
                        <td>
                          @php
                            $statusClass =
                                [
                                    'pending_payment' => 'badge-warning badge-pending',
                                    'paid' => 'badge-success badge-paid',
                                    'cancelled' => 'badge-danger badge-cancelled',
                                    'refunded' => 'badge-secondary badge-refunded',
                                ][$audience->payment_status] ?? 'badge-secondary';
                          @endphp
                          <span class="badge badge-status {{ $statusClass }}">
                            {{ Str::headline(str_replace('_', ' ', $audience->payment_status)) }}
                          </span>
                        </td>
                        <td>
                          {{ $audience->paper_title ?? 'N/A' }}
                          @if ($audience->full_paper_path)
                            <a href="{{ Storage::url($audience->full_paper_path) }}" target="_blank"
                              class="btn btn-sm btn-info"><i class="fas fa-download"></i> Paper</a>
                          @else
                            -
                          @endif
                        </td>
                        <td>
                            @if(($audience->keynote || $audience->parallelSession) && $audience->conference->certificate_template_position)
                            <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('home.audience.download', $audience->id)}}">
                              <i class="fas fa-download"></i>Download</a>
                            @endif
                        </td>
                        <td class="text-center">
                          <a href="{{ route('audience.show', $audience->public_id) }}" class="btn btn-info btn-sm"
                            title="Lihat"><i class="fas fa-eye"></i></a>
                          <a href="{{ route('audience.edit', $audience->public_id) }}" class="btn btn-warning btn-sm"
                              title="Lihat"><i class="fas fa-edit"></i></a>
                          <form action="{{ route('audience.destroy', $audience->public_id) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                              onclick="return confirm('Yakin ingin menghapus audience ini?')"><i
                                class="fas fa-trash"></i></button>
                          </form>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="10" class="text-center">Belum ada data audience.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

  @stop

  @section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <style>
      .table img.cover-thumb {
        max-width: 80px;
        height: auto;
        border-radius: 5px;
        object-fit: cover;
      }

      .badge-status {
        padding: 0.5em 0.75em;
        border-radius: 0.25rem;
        font-size: 0.75em;
      }

      .badge-pending {
        background-color: #ffc107;
        color: #333;
      }

      /* warning */
      .badge-paid {
        background-color: #28a745;
        color: #fff;
      }

      /* success */
      .badge-cancelled {
        background-color: #dc3545;
        color: #fff;
      }

      /* danger */
      .badge-refunded {
        background-color: #6c757d;
        color: #fff;
      }

      /* secondary */
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
        $('#audiencesTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
          },
          "responsive": true,
          "order": [[0, "desc"]], // kolom pertama (ID) urut desc
          "columnDefs": [{
              "orderable": false,
              "targets": [8, 9]
            }, // Kolom Paper dan Aksi tidak bisa disort
            {
              "searchable": false,
              "targets": [8, 9]
            } // Kolom Paper dan Aksi tidak bisa dicari
          ]
        });
      });
    </script>


  @stop
