@extends('layouts.admin')

@section('heading', $promotion ? 'Редактирование акции' : 'Добавление акции')

@section('content')
    <form class="auth-form" method="post"
        action="{{ $promotion ? route('admin.promotions.update', $promotion->promotion_id) : route('admin.promotions.store') }}">
        @csrf
        @if ($promotion)
            @method('PATCH')
        @endif

        @foreach ([
            'title' => ['Название акции', 'text'],
            'starts_at' => ['Дата начала', 'date'],
            'ends_at' => ['Дата окончания', 'date'],
        ] as $field => [$label, $type])
            @php($value = old($field, $promotion?->{$field}))
            <div class="auth-group">
                <label class="auth-label" for="promotion-{{ $field }}">{{ $label }}</label>
                <input class="auth-input" id="promotion-{{ $field }}" type="{{ $type }}" name="{{ $field }}"
                    value="{{ is_string($value) ? $value : '' }}" required
                    @if ($field === 'title') maxlength="200" @endif
                    @if ($errors->has($field)) aria-invalid="true" @endif>
            </div>
        @endforeach

        @php($description = old('description', $promotion?->description))
        <div class="auth-group">
            <label class="auth-label" for="promotion-description">Описание</label>
            <textarea class="auth-input" id="promotion-description" name="description" rows="4" maxlength="10000" required
                @if ($errors->has('description')) aria-invalid="true" @endif>{{ is_string($description) ? $description : '' }}</textarea>
        </div>

        @php($checkedTours = array_map('strval', array_filter((array) old('tour_ids', session()->hasOldInput() ? [] : $selectedTours), 'is_scalar')))
        <fieldset class="promotion-tour-options">
            <legend class="auth-label">Туры по акции</legend>
            @forelse ($tours as $tour)
                <label>
                    <input type="checkbox" name="tour_ids[]" value="{{ $tour->tour_id }}"
                        @checked(in_array((string) $tour->tour_id, $checkedTours, true))>
                    <span>{{ $tour->title }} · {{ \Illuminate\Support\Carbon::parse($tour->departure_date)->format('d.m.Y') }}
                        @if ($tour->archived_at) · В архиве @endif
                    </span>
                </label>
            @empty
                <p>В каталоге пока нет туров.</p>
            @endforelse
        </fieldset>

        <button class="auth-submit" type="submit">Сохранить</button>
    </form>
    <p class="auth-alt-text"><a class="auth-link" href="{{ route('admin.promotions.index') }}">Отмена</a></p>
@endsection
