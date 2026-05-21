<?php

namespace App\Http\Requests;

use App\Models\Notebook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('notebook') instanceof Notebook;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Notebook|null $notebook */
        $notebook = $this->route('notebook');

        return [
            'prompt' => ['required', 'string', 'max:4000'],
            'mode' => ['nullable', Rule::in(['qa', 'summary', 'report', 'brief', 'compare'])],
            'stream' => ['nullable', 'boolean'],
            'selected_source_ids' => ['nullable', 'array'],
            'selected_source_ids.*' => [
                'integer',
                Rule::exists('sources', 'id')->when($notebook, fn ($rule) => $rule->where('notebook_id', $notebook->id)),
            ],
        ];
    }
}
