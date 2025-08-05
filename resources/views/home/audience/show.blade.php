@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <div class="content-wrapper" style="min-height: 80vh; margin-left: 0;">
            <section class="content-header"><div class="container-fluid"><h1 class="m-0">Audience Details</h1></div></section>
            <section class="content">
                <div class="container-fluid">
                    <table id="audiencesTable" class="table table-bordered table-hover table-striped">
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
                            <td>{{ $audience->email }}</td>
                        </tr>
                        <tr>
                            <th>Biaya Dibayar</th>
                            <td>{{ $audience->email }}</td>
                        </tr>
                        <tr>
                            <th>Status Pembayaran</th>
                            <td>
                                @php
                                    $statusClass = [
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
                        <tr>
                            <th>Paper</th>
                            <td>
                                @if($audience->paper)
                                    <a href="{{ asset($audience->paper->full_paper_path) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-file"></i> View Paper
                                    </a>
                                @else
                                    <span class="text-muted">No Paper Submitted</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <!-- <a href="{{ route('audience.index') }}" class="btn btn-primary">Back to Audience List</a> -->
                </div>
            </section>
        </div>
    </div>
</body>
@stop