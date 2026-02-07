<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FinalBossStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:final_bosses,slug'],
            'lore' => ['nullable', 'string'],
            'sprite_path' => ['nullable', 'string', 'max:255'],
            'base_stats_json' => ['required', 'array'],
            'base_stats_json.hp' => ['required', 'integer', 'min:0'],
            'base_stats_json.damage' => ['required', 'integer', 'min:0'],
            'base_stats_json.defense' => ['required', 'integer', 'min:0'],
        ];
    }
}
