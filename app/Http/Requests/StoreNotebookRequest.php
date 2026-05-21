<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotebookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'visibility' => $this->input('visibility', 'shared'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'visibility' => ['required', Rule::in(['private', 'shared'])],
            'status' => ['required', Rule::in(['draft', 'active', 'archived'])],
            'icon' => ['nullable', 'string', 'max:40'],
            'cover_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'smart_tags' => ['nullable', 'array'],
            'smart_tags.*' => ['string', 'max:40'],
        ];
    }
}
