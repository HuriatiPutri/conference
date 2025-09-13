@php
  use App\Constants\Countries;
@endphp
@extends('adminlte::page')

@section('title', 'Keynote Session Management')

@section('content_header')

@stop

@section('content')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
      <div class="content-wrapper" style="min-height: 80vh; margin-left: 0;">
        <section class="content-header">
          <div class="container-fluid">
            <h1 class="m-0">Keynote Session</h1>
          </div>
        </section>
        <section class="content">
          <div class="container-fluid">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">Keynote Session</h3>
              <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Asal Institusi</th>
                            <th>Keynote Session</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($keynotes as $index => $keynote)
                            <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $keynote->name }}</td>
                            <td>{{ $keynote->email }}</td>
                            <td>{{ $keynote->institution }}</td>
                            <td>{{ $keynote->keynote ? 'Yes' : 'No' }}</td>
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
