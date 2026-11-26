@extends('layouts.auth')

@section('container-class', 'admin-container')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('before-heading')
    <nav class="admin-nav" aria-label="Управление сайтом">
        @can('manage-catalog')
            <a href="{{ route('admin.tours.index') }}"
                @if (request()->routeIs('admin.tours.*')) aria-current="true" @endif>Туры</a>
        @endcan
        @can('manage-bookings')
            <a href="{{ route('admin.bookings.index') }}"
                @if (request()->routeIs('admin.bookings.*')) aria-current="true" @endif>Заявки</a>
        @endcan
        @can('manage-promotions')
            <a href="{{ route('admin.promotions.index') }}"
                @if (request()->routeIs('admin.promotions.*')) aria-current="true" @endif>Акции</a>
        @endcan
        <a class="admin-nav-account" href="{{ route('account') }}">Личный кабинет</a>
    </nav>
@endsection
