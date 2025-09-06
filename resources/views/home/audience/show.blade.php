
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
            <h1 class="m-0">Audience Details</h1>
          </div>
        </section>
        <section class="content">
          <div class="container-fluid responsive">
            <table id="audiencesTable" class="table-bordered table-hover table-striped table">
              <tr>
                <th>Conference</th>
                <td>{{ $audience->conference->name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Name</th>
                <td>{{ $audience->first_name }} {{ $audience->last_name }}</td>
              </tr>
              <tr>
                <th>Email</th>
                <td>{{ $audience->email }}</td>
              </tr>
              <tr>
                <th>No Handphone</th>
                <td><a href="https://wa.me/{{ $audience->phone_number }}" target="_blank">
                    {{ $audience->phone_number }}</a></td>
              <tr>
                <th>Tipe Partisipan</th>
                <td>{{ Str::headline($audience->presentation_type) }}</td>
              </tr>
              <tr>
                <th>Asal Institusi</th>
                <td>{{ $audience->institution ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Negara</th>
                <td>{{ $audience->country_name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Metode Pembayaran</th>
                <td>{{ $audience->getPaymentMethodText() }}</td>
              </tr>
              <tr>
                <th>Biaya Dibayar</th>
                <td>{{ $audience->country === 'ID' ? 'Rp' : 'USD' }}{{ $audience->paid_fee }}</td>
              </tr>
              <tr>
                <th>Status Pembayaran</th>
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
              </tr>
              @if ($audience->payment_method === 'transfer_bank')
                <tr>
                  <th>Proof of Payment</th>
                  <td>
                    @if ($audience->payment_proof_path)
                      <a href="{{ Storage::url($audience->payment_proof_path) }}" target="_blank"
                        class="btn btn-primary btn-sm">
                        <i class="fas fa-file"></i> View Proof
                      </a>
                    @else
                      <span class="text-muted">No Proof Uploaded</span>
                    @endif
                  </td>
                </tr>
              @endif
              <tr>
                <th>Judul Paper</th>
                <td>{{ $audience->paper_title ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Paper</th>
                <td>
                  @if ($audience->full_paper_path)
                    <a href="{{ Storage::url($audience->full_paper_path) }}" target="_blank"
                      class="btn btn-sm btn-info"><i class="fas fa-download"></i> Paper</a>
                  @else
                    -
                  @endif
                </td>
              </tr>
              <tr>
                <th>Sertifikat</th>
                <td>
                  @if (($audience->keynote || $audience->parallelSession) && $audience->conference->certificate_template_position)
                    <a class="btn btn-primary btn-sm" target="_blank"
                      href="{{ route('home.audience.download', $audience->id) }}">
                      <i class="fas fa-download"></i> Download</a>
                  @else
                    -
                  @endif
                </td>
              </tr>
              <tr>
                <th>Tanggal Registrasi</th>
                <td>{{ $audience->created_at ?? 'N/A' }}</td>
              </tr>
            </table>
            <!-- <a href="{{ route('audience.index') }}" class="btn btn-primary">Back to Audience List</a> -->
          </div>
        </section>
      </div>
    </div>
  </body>
@stop
