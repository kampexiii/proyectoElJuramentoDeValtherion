<?php

declare(strict_types=1);

namespace App\Http\Requests\Missions;

use Illuminate\Foundation\Http\FormRequest;

class SubmitBossActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:attack,defend,magic,potion'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Debes elegir una accion.',
            'action.in' => 'Accion invalida.',
        ];
    }
}
