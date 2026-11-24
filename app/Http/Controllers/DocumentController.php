<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as Validation;
use Illuminate\Support\MessageBag;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        return view('documents', [
            'documents' => $this->documents($request)->orderByDesc('document_id')->get(),
        ]);
    }

    public function create(): Response
    {
        return $this->form();
    }

    public function store(Request $request): Response|RedirectResponse
    {
        $validator = $this->validateDocument($request);
        if ($validator->fails()) {
            return $this->form(validator: $validator);
        }

        DB::table('user_documents')->insert($validator->validated() + ['user_id' => $request->user()->getKey()]);

        return to_route('documents', [], 303)->with('document_status', 'Документ добавлен.');
    }

    public function edit(Request $request, string $documentId): Response
    {
        return $this->form($this->findDocument($this->documents($request)->where('document_id', $documentId)));
    }

    public function update(Request $request, string $documentId): Response|RedirectResponse
    {
        $query = $this->documents($request)->where('document_id', $documentId);
        $document = $this->findDocument($query);
        $validator = $this->validateDocument($request);
        if ($validator->fails()) {
            return $this->form($document, $validator);
        }

        abort_if($query->update($validator->validated()) === 0 && ! $query->exists(), 404);

        return to_route('documents', [], 303)->with('document_status', 'Изменения сохранены.');
    }

    public function confirmDelete(Request $request, string $documentId): View
    {
        return view('documents-delete', [
            'document' => $this->findDocument($this->documents($request)->where('document_id', $documentId)),
        ]);
    }

    public function destroy(Request $request, string $documentId): RedirectResponse
    {
        abort_if($this->documents($request)->where('document_id', $documentId)->delete() === 0, 404);

        return to_route('documents', [], 303)->with('document_status', 'Документ удалён.');
    }

    private function documents(Request $request): Builder
    {
        return DB::table('user_documents')->where('user_id', $request->user()->getKey());
    }

    private function findDocument(Builder $query): object
    {
        $document = $query->first();
        abort_if($document === null, 404);

        return $document;
    }

    private function validateDocument(Request $request): Validator
    {
        return Validation::make($request->only('document_type', 'series', 'number', 'issued_at', 'expires_at'), [
            'document_type' => ['required', 'string', 'max:80'],
            'series' => ['nullable', 'string', 'max:30'],
            'number' => ['required', 'string', 'max:40'],
            'issued_at' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1000-01-01'],
            'expires_at' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1000-01-01'],
        ], [
            'required' => 'Заполни поле «:attribute».',
            'string' => 'Проверь поле «:attribute».',
            'max' => 'В поле «:attribute» допустимо не более :max символов.',
            'date_format' => 'Проверь дату в поле «:attribute».',
            'after_or_equal' => 'Проверь год в поле «:attribute».',
        ], [
            'document_type' => 'Тип документа',
            'series' => 'Серия',
            'number' => 'Номер',
            'issued_at' => 'Дата выдачи',
            'expires_at' => 'Действителен до',
        ])->after(function (Validator $validator): void {
            $data = $validator->getData();
            if (! $validator->errors()->hasAny(['issued_at', 'expires_at'])
                && isset($data['issued_at'], $data['expires_at'])
                && $data['expires_at'] < $data['issued_at']) {
                $validator->errors()->add('expires_at', 'Дата окончания действия не может быть раньше даты выдачи.');
            }
        });
    }

    private function form(?object $document = null, ?Validator $validator = null): Response
    {
        $values = array_replace((array) $document, $validator?->getData() ?? []);

        return response()->view('documents-form', [
            'document' => $document,
            'values' => $values,
            'errors' => $validator?->errors() ?? new MessageBag,
        ], $validator ? 422 : 200)->header('Cache-Control', 'private, no-store');
    }
}
