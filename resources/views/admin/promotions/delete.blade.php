@extends('layouts.admin')

@section('heading', 'Удаление акции')

@section('content')
    <p class="auth-feedback">Удалить акцию «{{ $promotion->title }}»? Связанные туры останутся в каталоге.</p>
    <form class="auth-form" action="{{ route('admin.promotions.destroy', $promotion->promotion_id) }}" method="post">
        @csrf
        @method('DELETE')
        <button type="submit" class="auth-submit admin-submit-danger">Удалить акцию</button>
    </form>
    <p class="auth-alt-text"><a class="auth-link" href="{{ route('admin.promotions.index') }}">Отмена</a></p>
@endsection
