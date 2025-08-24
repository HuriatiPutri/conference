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
          <div class="container-fluid">
            <form action="{{ route('audience.update', $audience->public_id) }}" method="POST" enctype="multipart/form-data">              
                @csrf
                @method('PUT') 
                <input type="hidden" name="id" value="{{ $audience->id }}">
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
                  <th>Tipe Partisipan</th>
                  <td>{{ $audience->presentation_type }}</td>
                </tr>
                <tr>
                  <th>Biaya Dibayar</th>
                  <td>{{ $audience->paid_fee }}</td>
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
                    @if ($audience->payment_method !== 'transfer_bank')
                      <span class="badge badge-status {{ $statusClass }}">
                        {{ Str::headline(str_replace('_', ' ', $audience->payment_status)) }}
                      </span>
                    @else
                      <select name="payment_status" class="form-control">
                        <option value="pending_payment"
                          {{ $audience->payment_status == 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                        <option value="paid" {{ $audience->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ $audience->payment_status == 'cancelled' ? 'selected' : '' }}>
                          Cancelled</option>
                        <option value="refunded" {{ $audience->payment_status == 'refunded' ? 'selected' : '' }}>Refunded
                        </option>
                      </select>
                      <button type="submit" class="btn btn-primary btn-sm mt-2">Update Status</button>
                    @endif
                  </td>
                </tr>
                @if ($audience->payment_method === 'transfer_bank')
                  <tr>
                    <th>Proof of Payment</th>
                    <td>
                      @if ($audience->proof_of_payment_path)
                        <a href="{{ asset($audience->proof_of_payment_path) }}" target="_blank"
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
                  <th>Paper</th>
                  <td>
                    @if ($audience->paper)
                      <a href="{{ asset($audience->paper->full_paper_path) }}" target="_blank"
                        class="btn btn-primary btn-sm">
                        <i class="fas fa-file"></i> View Paper
                      </a>
                    @else
                      <span class="text-muted">No Paper Submitted</span>
                    @endif
                  </td>
                </tr>
              </table>
            </form>
            <a href="{{ route('audience.index') }}" class="btn btn-primary">Back to Audience List</a>
          </div>
        </section>
      </div>
    </div>
  </body>
@stop
