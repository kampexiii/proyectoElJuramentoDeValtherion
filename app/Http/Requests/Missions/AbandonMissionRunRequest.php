<?php

namespace App\Http\Requests\Missions;

use Illuminate\Foundation\Http\FormRequest;

class AbandonMissionRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'partial' => ['sometimes', 'boolean'],
        ];
    }
}
