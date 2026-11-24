@extends('layouts.auth')

@section('heading', $document ? 'Редактирование документа' : 'Добавление документа')

@section('content')
    <form class="auth-form" method="post" autocomplete="off"
        action="{{ $document ? route('documents.update', $document->document_id) : route('documents.store') }}">
        @csrf
        @if ($document)
            @method('PATCH')
        @endif

        @foreach ([
            'document_type' => ['Тип документа', 'text', 80, true],
            'series' => ['Серия', 'text', 30, false],
            'number' => ['Номер', 'text', 40, true],
            'issued_at' => ['Дата выдачи', 'date', null, false],
            'expires_at' => ['Действителен до', 'date', null, false],
        ] as $field => [$label, $type, $maxLength, $required])
            @php($value = $values[$field] ?? '')
            <div class="auth-group">
                <label class="auth-label" for="{{ $field }}">{{ $label }}</label>
                <input class="auth-input" type="{{ $type }}" id="{{ $field }}" name="{{ $field }}"
                    value="{{ is_string($value) ? $value : '' }}"
                    @if ($maxLength) maxlength="{{ $maxLength }}" @endif
                    @if ($field === 'document_type') placeholder="Например, загранпаспорт" @endif
                    @if ($errors->has($field)) aria-invalid="true" @endif
                    @required($required)>
            </div>
        @endforeach

        <button type="submit" class="auth-submit">Сохранить</button>
    </form>
    <p class="auth-alt-text"><a class="auth-link" href="{{ route('documents') }}">Отмена</a></p>
@endsection
