@extends('layouts.admin')

@section('heading', 'Убрать тур из каталога')

@section('content')
    <p class="auth-feedback">Убрать тур «{{ $tour->title }}» из каталога? Существующие заявки сохранятся.</p>
    <form class="auth-form" action="{{ route('admin.tours.archive', $tour->tour_id) }}" method="post">
        @csrf
        @method('DELETE')
        <button class="auth-submit admin-submit-danger" type="submit">Убрать тур</button>
    </form>
    <p class="auth-alt-text"><a class="auth-link" href="{{ route('admin.tours.index') }}">Отмена</a></p>
@endsection
