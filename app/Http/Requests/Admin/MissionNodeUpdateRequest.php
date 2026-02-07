<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MissionNodeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'step_index' => ['required', 'integer', 'between:1,6'],
            'is_start' => ['nullable', 'boolean'],
            'title' => ['nullable', 'string', 'max:255'],
            'body_text' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $step = (int) $this->input('step_index');
            if ($this->boolean('is_start') && $step !== 1) {
                $validator->errors()->add('is_start', 'Solo se permite marcar inicio en el paso 1.');
            }
        });
    }
}
