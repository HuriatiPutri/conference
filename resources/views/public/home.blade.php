@extends('layouts.app')
@section('title', 'Detail Pendaftaran Peserta Konferensi')

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

@section('content')
    <div>
        <h1>Welcome to the SOTVI Conference Management System</h1>
        <br/>
        <p>Welcome to the official page of the <strong>SOTVI Conference Management System</strong>.</p>
        <p>Here, you will find the latest information about the upcoming conference including registration details, speaker lineups, and the full event agenda.</p>
        <p><strong>Interested in joining as a participant?</strong>
            <br/>Visit the Registration page and follow the provided instructions. Be sure to complete all required information and proceed with the payment as directed.
        <p><strong>Have questions or need assistance?</strong>
            <br/>Our team is ready to help. Please visit the Contact page to reach out to us.</p>

        <p>Thank you for visiting.<br/>We look forward to welcoming you to the SOTVI Conference and hope it will be an inspiring and valuable experience for you.</p>
    </div>
@endsection
