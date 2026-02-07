<?php

namespace App\Http\Requests\Missions;

use Illuminate\Foundation\Http\FormRequest;

class ChooseMissionOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'choice_id' => ['required', 'integer', 'exists:mission_choices,id'],
        ];
    }
}
