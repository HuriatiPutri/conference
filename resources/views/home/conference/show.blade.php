@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <div class="content-wrapper" style="min-height: 80vh; margin-left: 0;">
            <section class="content-header"><div class="container-fluid"><h1 class="m-0">Conference Details</h1></div></section>
            <section class="content">
                <div class="container-fluid">
                    <table id="audiencesTable" class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>Conference</th>
                            <td>{{ $conference->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Inisial</th>
                            <td>{{ $conference->initial ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $conference->date ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Poster</th>
                            <td> @if($conference->cover_poster_path)
                                <img src="{{ Storage::url($conference->cover_poster_path) }}" alt="Poster {{ $conference->name }}" height="250" width="250" class="cover-thumb img-thumbnail">
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Kota</th>
                            <td>{{ $conference->city ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Negara</th>
                            <td>{{ $conference->country ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Biaya Online</th>
                            <td>
                                Rp {{ number_format($conference->online_fee, 0, ',', '.') }}<br/>
                                (USD {{ number_format($conference->online_fee_usd, 2, '.', ',') }})
                            </td>
                        </tr>
                        <tr>
                            <th>Biaya Onsite</th>
                            <td>Rp {{ number_format($conference->onsite_fee, 0, ',', '.') }}
                                <br/>
                                (USD {{ number_format($conference->onsite_fee_usd, 2, '.', ',') }})
                            </td>
                        </tr>
                        <tr>
                            <th>Biaya Partisipan</th>
                            <td>Rp {{ number_format($conference->participant_fee, 0, ',', '.') }}
                                <br/>
                                (USD {{ number_format($conference->participant_fee_usd, 2, '.', ',') }})
                            </td>
                        </tr>
                        <tr>
                            <th>Link Registrasi</th>
                            <td>
                                @php
                                    $registrationUrl = route('registration.create', $conference->public_id);
                                    $keynoteUrl = route('keynote.index', $conference->public_id);
                                    $parallelSessionUrl = route('parallel-session.index', $conference->public_id);
                                @endphp
                                <i class="fas fa-copy copy-button"
                                    data-url="{{ $registrationUrl }}"
                                    title="Salin Link Registrasi"></i>
                            </td>
                        </tr>
                        <tr>
                            <th>Link Keynote</th>
                            <td>
                                <i class="fas fa-copy copy-button"
                                    data-url="{{ $keynoteUrl }}"
                                    title="Salin Link Keynote"></i>
                            </td>
                        </tr>
                        <tr>
                            <th>Link Parallel Session</th>
                            <td>
                                <i class="fas fa-copy copy-button"
                                    data-url="{{ $parallelSessionUrl }}"
                                    title="Salin Link Parallel Session"></i>
                            </td>
                        </tr>
                    </table> 
                    <a href="{{ route('conference.index') }}" class="btn btn-primary">Back to Conference List</a>
                </div>
            </section>
        </div>
    </div>
</body>
@stop