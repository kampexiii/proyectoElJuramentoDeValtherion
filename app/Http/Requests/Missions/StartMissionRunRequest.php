<?php

namespace App\Http\Requests\Missions;

use Illuminate\Foundation\Http\FormRequest;

class StartMissionRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
