@php
  use App\Constants\Countries;
@endphp
@extends('adminlte::page')

@section('title', 'Parallel Session Management')

@section('content_header')

@stop

@section('content')

  <body class="hold-transition sidebar-mini">
    <div class="wrapper">
      <div class="content-wrapper" style="min-height: 80vh; margin-left: 0;">
        <section class="content-header">
          <div class="container-fluid">
            <h1 class="m-0">Parallel Session</h1>
          </div>
        </section>
        <section class="content">
          <div class="container-fluid">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Parallel Session</h3>
              </div>
              <div class="card-body">
                <h5>Filter Data Parallel Session</h5>
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
                </div>
                <div class="table-responsive">
                  <table class="table-bordered table-hover table" id="parallelSessionsTable">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Conference</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Judul Paper</th>
                        <th>Ruangan</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($parallels as $index => $parallel)
                        <tr>
                          <td>{{ $index + 1 }}</td>
                          <td>{{ $parallel->audience->conference->name }}</td>
                          <td>{{ $parallel->name_of_presenter }}</td>
                          <td>{{ $parallel->audience->email }}</td>
                          <td>{{ $parallel->paper_title }}</td>
                          <td>{{ $parallel->room->room_name }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </body>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
  @stop

@section('js')
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
      var table = $('#parallelSessionsTable').DataTable({
        "dom": 'Bfrtip',
        "buttons": [{
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel"></i> Export to Excel',
            className: 'btn btn-info mb-3',
            title: 'Daftar Parallel Session',
            exportOptions: {
              columns: ':not(:last-child)' // Exclude the last column (Aksi)
            }
          },
          {
            extend: 'csvHtml5',
            text: '<i class="fas fa-file-csv"></i> Export to CSV',
            className: 'btn btn-info mb-3',
            title: 'Daftar Parallel Session',
            exportOptions: {
              columns: ':not(:last-child)' // Exclude the last column (Aksi)
            }
          },
          {
            extend: 'print',
            text: '<i class="fas fa-print"></i> Print',
            className: 'btn btn-info mb-3',
            title: 'Daftar Parallel Session',
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

      $('#filterConference').on('change', function() {
        var val = $(this).val();
        table.column(1).search(val ? '^' + regexEscape(val) + '$' : '', true, false).draw();
      });
    });
  </script>


@stop
