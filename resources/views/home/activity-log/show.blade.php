@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <div class="content-wrapper" style="min-height: 80vh; margin-left: 0;">
            <section class="content-header"><div class="container-fluid"><h1 class="m-0">Activity Log Details</h1></div></section>
            <section class="content">
                <div class="container-fluid">
                    <table id="audiencesTable" class="table table-bordered table-hover table-striped">
                        <tr>
                            <th>Level</th>
                            <td>{{ $activityLog->level ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Message</th>
                            <td>{{ $activityLog->message ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Data</th>
                            <td><pre>{{ json_encode($activityLog->context, JSON_PRETTY_PRINT) }}</pre></td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $activityLog->created_at ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $activityLog->updated_at ?? 'N/A' }}</td>
                        </tr>
                    </table> 
                    <a href="{{ route('conference.index') }}" class="btn btn-primary">Back to Conference List</a>
                </div>
            </section>
        </div>
    </div>
</body>
@stop