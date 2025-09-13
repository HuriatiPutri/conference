@php
  use App\Constants\Countries;
@endphp
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
                <h5>Filter Data Audience</h5>
                <div class="row">
                  <div class="col-md-4">
                    <div class="mb-3 flex gap-4">
                      <select id="filterConference" class="form-control">
                        <option value="">-- Semua Conference --</option>
                        @foreach ($conferences as $conference)
                          <option value="{{ $conference->name }}">{{ $conference->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <select id="filterPaymentMethod" class="form-control">
                      <option value="">-- Semua Metode Pembayaran --</option>
                      <option value="Bank Transfer">Bank Transfer</option>
                      <option value="Payment Gateway">Payment Gateway</option>
                      <!-- tambah sesuai kebutuhan -->
                    </select>
                  </div>
                  <div class="col-md-4 mb-3">
                    <select id="filterPaymentStatus" class="form-control">
                      <option value="">-- Semua Status Pembayaran --</option>
                      <option value="Paid">Paid</option>
                      <option value="Pending Payment">Payment Pending</option>
                      <option value="Cancelled">Cancelled</option>
                      <option value="Refunded">Refunded</option>
                      <!-- tambah sesuai kebutuhan -->
                    </select>
                  </div>
                </div>

                <div class="mb-3 flex flex-col gap-6 sm:flex-row">
                  <span id="summaryPaid" class="bg-green rounded p-2">Paid 0</span>
                  <span id="summaryPending" class="bg-yellow rounded p-2">Pending Payment 0</span>
                  <span id="summaryExpired" class="bg-red rounded p-2">Cancelled/Refunded 0</span>
                  <span id="summaryRefunded" class="bg-gray rounded p-2">Refunded 0</span>
                </div>
                <hr />
                <div class="table-responsive">
                  <table id="audiencesTable" class="table-bordered table-hover table-striped table">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Konferensi</th>
                        <th>Nama Depan</th>
                        <th>Nama Belakang</th>
                        <th>Email</th>
                        <th>No Handphone</th>
                        <th>Tipe Partisipan</th>
                        <th>Metode Pembayaran</th>
                        <th>Biaya Dibayar</th>
                        <th>Status Pembayaran</th>
                        <th>Paper</th>
                        <th>Keynote Session</th>
                        <th>Parallel Session</th>
                        <th>Sertifikat</th>
                        <th class="text-center" width="300px">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($audiences as $audience)
                        @php
                          if ($audience->phone_number) {
                              $clearPhoneNumber = preg_replace('/[\s\-\(\)\+]/', '', $audience->phone_number);
                              if (preg_match('/^0/', $clearPhoneNumber) && isset(Countries::LIST[$audience->country])) {
                                  $phoneNumber = preg_replace(
                                      '/^0/',
                                      Countries::LIST[$audience->country]['code'],
                                      $clearPhoneNumber,
                                  );
                              } elseif (preg_match('/^(?!\+|0)/', $clearPhoneNumber)) {
                                  $phoneNumber = $clearPhoneNumber;
                              } else {
                                  $phoneNumber =
                                      (isset(Countries::LIST[$audience->country])
                                          ? Countries::LIST[$audience->country]['code']
                                          : '') . $clearPhoneNumber;
                              }
                              $audience->phone_number = $phoneNumber;
                          }
                        @endphp
                        <tr>
                          <td>{{ $audience->id }}</td>
                          <td>{{ $audience->conference->name ?? 'N/A' }}</td>
                          <td>{{ $audience->first_name }}</td>
                          <td>{{ $audience->last_name }}</td>
                          <td>{{ $audience->email }}</td>
                          <td><a href="https://wa.me/{{ $audience->phone_number }}" target="_blank">
                              {{ $audience->phone_number }}</a></td>
                          <td>{{ Str::headline($audience->presentation_type) }}</td>
                          <td>
                            {{ $audience->getPaymentMethodText() }}
                            @if ($audience->payment_method === 'transfer_bank' && $audience->payment_proof_path)
                              <br>
                              <a href="{{ Storage::url($audience->payment_proof_path) }}" class="mt-2"
                                target="_blank">(Proof Payment)</a>
                            @endif
                          </td>
                          <td>{{ $audience->country === 'ID' ? 'Rp' : 'USD' }}
                            {{ number_format($audience->paid_fee, 0, ',', '.') }}</td>
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
                          <td><input type="checkbox" {{ $audience->keynote ? 'checked' : '' }}/></td>
                          <td><input type="checkbox"  {{ $audience->parallelSession ? 'checked' : '' }} /></td>
                          <td>
                            @if (($audience->keynote || $audience->parallelSession) && $audience->conference->certificate_template_position)
                              <a class="btn btn-primary btn-sm" target="_blank"
                                href="{{ route('home.audience.download', $audience->public_id) }}">
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


    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">


    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Library untuk Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script>
      function regexEscape(text) {
        return text.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      }
      $(document).ready(function() {
        var table = $('#audiencesTable').DataTable({
          "dom": 'Bfrtip',
          "buttons": [
            {
              extend: 'excelHtml5',
              text: '<i class="fas fa-file-excel"></i> Export to Excel',
              className: 'btn btn-info mb-3',
              title: 'Daftar Audience',
              exportOptions: {
                columns: ':not(:last-child)' // Exclude the last column (Aksi)
              }
            },
            {
              extend: 'csvHtml5',
              text: '<i class="fas fa-file-csv"></i> Export to CSV',
              className: 'btn btn-info mb-3',
              title: 'Daftar Audience',
              exportOptions: {
                columns: ':not(:last-child)' // Exclude the last column (Aksi)
              }
            },
            {
              extend: 'print',
              text: '<i class="fas fa-print"></i> Print',
              className: 'btn btn-info mb-3',
              title: 'Daftar Audience',
              exportOptions: {
                columns: ':not(:last-child)' // Exclude the last column (Aksi)
              }
            }
          ],
          "language": {
            "url": "//cdn.datatables.net/plug-ins/2.0.8/i18n/id.json"
          },
          "responsive": true,
          "order": [
            [0, "desc"]
          ], // kolom pertama (ID) urut desc
          "columnDefs": [{
              "orderable": false,
              "targets": [],
            }, // Kolom Paper dan Aksi tidak bisa disort
            {
              "searchable": false,
              "targets": []
            } // Kolom Paper dan Aksi tidak bisa dicari
          ]
        });

        updateSummary();

        $('#filterConference').on('change', function() {
          var val = $(this).val();
          table.column(1).search(val ? '^' + regexEscape(val) + '$' : '', true, false).draw();
        });

        $('#filterPaymentMethod').on('change', function() {
          var val = $(this).val();
          table.column(7).search(val ? val : '', true, false).draw();
        });

        $('#filterPaymentStatus').on('change', function() {
          var val = $(this).val();
          table.column(9).search(val ? val : '', true, false).draw();
        });

        table.on('draw', function() {
          updateSummary();
        });

        function updateSummary() {
          var data = table.rows({
            filter: 'applied'
          }).data();
          var transferCount = 0;
          var gatewayCount = 0;
          var cancelledCount = 0;
          var refundedCount = 0;

          data.each(function(row) {
            // asumsi kolom payment_method di index 12
            var payment = row[9];
            if (payment.toLowerCase().includes('paid')) {
              transferCount++;
            } else if (payment.toLowerCase().includes('pending payment')) {
              gatewayCount++;
            } else if (payment.toLowerCase().includes('cancelled')) {
              cancelledCount++;
            } else if (payment.toLowerCase().includes('refunded')) {
              refundedCount++;
            }
          });

          $('#summaryPaid').text('Paid: ' + transferCount);
          $('#summaryPending').text('Pending: ' + gatewayCount);
          $('#summaryCancelled').text('Cancelled/Refunded: ' + cancelledCount);
          $('#summaryRefunded').text('Refunded: ' + refundedCount);
        }
      });
    </script>


  @stop
