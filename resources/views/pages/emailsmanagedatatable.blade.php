@extends('layouts.app', ['activePage' => 'leads-management', 'title' => 'Cruiser Travels Leadbox Management System', 'navName' => 'Leads Management', 'activeButton' => 'laravel'])

@section('content')
    {{$dataTable->table()}}
@endsection


@push('scripts')
    {{$dataTable->scripts()}}
@endpush