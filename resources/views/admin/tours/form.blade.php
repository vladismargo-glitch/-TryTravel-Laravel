@extends('layouts.admin')

@section('heading', $tour ? 'Редактирование тура' : 'Добавление тура')

@section('content')
    <form class="auth-form" method="post"
        action="{{ $tour ? route('admin.tours.update', $tour->tour_id) : route('admin.tours.store') }}">
        @csrf
        @if ($tour)
            @method('PATCH')
        @endif

        @php($categoryId = old('category_id', $tour?->category_id))
        <div class="auth-group">
            <label class="auth-label" for="category_id">Категория</label>
            <select class="auth-input" id="category_id" name="category_id" required
                @if ($errors->has('category_id')) aria-invalid="true" @endif>
                <option value="">Выбери категорию</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->category_id }}"
                        @selected(is_scalar($categoryId) && (string) $category->category_id === (string) $categoryId)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="admin-tour-fields">
            @foreach ([
                'title' => ['Название тура', 'text', 200],
                'country' => ['Страна', 'text', 100],
                'destination' => ['Курорт или город', 'text', 150],
                'departure_city' => ['Город вылета', 'text', 100],
                'departure_date' => ['Дата вылета', 'date', null],
                'nights' => ['Количество ночей', 'number', null],
                'meal_plan' => ['Питание', 'text', 80],
                'price' => ['Цена, ₽', 'text', 13],
            ] as $field => [$label, $type, $limit])
                @php($value = old($field, $tour?->{$field}))
                <div class="auth-group">
                    <label class="auth-label" for="{{ $field }}">{{ $label }}</label>
                    <input class="auth-input" type="{{ $type }}" id="{{ $field }}" name="{{ $field }}"
                        value="{{ is_scalar($value) ? $value : '' }}" required
                        @if ($limit) maxlength="{{ $limit }}" @endif
                        @if ($field === 'nights') min="1" max="65535" @endif
                        @if ($field === 'price') inputmode="decimal" @endif
                        @if ($errors->has($field)) aria-invalid="true" @endif>
                </div>
            @endforeach
        </div>

        <fieldset class="promotion-tour-options admin-guest-options">
            <legend class="auth-label">Состав туристов для поиска</legend>
            <div class="admin-tour-fields">
                @foreach (['max_adults' => ['Максимум взрослых', 1], 'max_children' => ['Максимум детей', 0]] as $field => [$label, $min])
                    @php($value = old($field, $tour?->{$field}))
                    <div class="auth-group">
                        <label class="auth-label" for="{{ $field }}">{{ $label }}</label>
                        <input class="auth-input" type="number" id="{{ $field }}" name="{{ $field }}" min="{{ $min }}" max="255"
                            value="{{ is_scalar($value) ? $value : '' }}" @if ($errors->has($field)) aria-invalid="true" @endif>
                    </div>
                @endforeach
            </div>
            <small>Оставь оба поля пустыми, если состав туристов для этого тура ещё неизвестен.</small>
        </fieldset>

        @php($image = old('image_url', $tour?->image_url))
        <div class="auth-group">
            <label class="auth-label" for="image_url">Изображение</label>
            <input class="auth-input" id="image_url" name="image_url" list="tour-images"
                value="{{ is_string($image) ? $image : '' }}" maxlength="500" required
                aria-describedby="tour-image-hint" @if ($errors->has('image_url')) aria-invalid="true" @endif>
            <datalist id="tour-images">
                @foreach ($images as $path)
                    <option value="{{ $path }}"></option>
                @endforeach
            </datalist>
            <small id="tour-image-hint">Выбери готовое изображение или вставь ссылку HTTPS.</small>
        </div>

        @php($description = old('description', $tour?->description))
        <div class="auth-group">
            <label class="auth-label" for="description">Описание</label>
            <textarea class="auth-input" name="description" id="description" rows="4" maxlength="10000"
                @if ($errors->has('description')) aria-invalid="true" @endif>{{ is_string($description) ? $description : '' }}</textarea>
        </div>

        <button class="auth-submit" type="submit">Сохранить</button>
    </form>
    <p class="auth-alt-text"><a class="auth-link" href="{{ route('admin.tours.index') }}">Отмена</a></p>
@endsection
