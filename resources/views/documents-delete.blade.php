@extends('layouts.auth')

@section('heading', 'Удаление документа')

@section('content')
    <p class="auth-feedback">Удалить документ «{{ $document->document_type }}» с номером {{ $document->number }}?</p>
    <form class="auth-form" method="post" action="{{ route('documents.destroy', $document->document_id) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="auth-submit">Удалить документ</button>
    </form>
    <p class="auth-alt-text"><a class="auth-link" href="{{ route('documents') }}">Отмена</a></p>
@endsection
