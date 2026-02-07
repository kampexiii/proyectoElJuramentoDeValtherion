<?php

namespace App\Http\Requests\Missions;

use Illuminate\Foundation\Http\FormRequest;

class FightBossRequest extends FormRequest
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
