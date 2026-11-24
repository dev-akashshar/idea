<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\IdeaStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreIdeaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('links')) {
            $this->merge([
                'links' => array_values(array_filter((array) $this->input('links'), fn ($link) => ! empty(trim((string) $link)))),
            ]);
        }

        if ($this->has('steps')) {
            $this->merge([
                'steps' => array_values(array_filter((array) $this->input('steps'), fn ($step) => ! empty(trim((string) $step)))),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'status' => ['required', Rule::enum(IdeaStatus::class)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'links' => ['nullable', 'array'],
            'links.*' => ['required', 'url'],
            'steps' => ['nullable', 'array'],
            'steps.*' => ['required', 'string', 'max:255'],
        ];
    }
}
