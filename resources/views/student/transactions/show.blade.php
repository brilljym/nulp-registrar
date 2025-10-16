@extends('layouts.app')

@section('content')
    <h2>Transaction Details</h2>
    <p><strong>Reference:</strong> {{ $transaction->request_id }}</p>
    <p><strong>Status:</strong> {{ ucfirst($transaction->status) }}</p>
    <!-- other fields -->
@endsection
