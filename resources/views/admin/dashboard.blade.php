{{-- resources/views/admin/dashboard.blade.php --}}
@extends('adminlte::page')

@section('title', 'Admin Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to your new AdminLTE panel.</p>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}" />
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop
