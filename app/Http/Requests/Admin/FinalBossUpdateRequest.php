<?php

namespace App\Http\Requests\Admin;

use App\Models\FinalBoss;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FinalBossUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var FinalBoss $boss */
        $boss = $this->route('final_boss');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('final_bosses', 'slug')->ignore($boss?->id),
            ],
            'lore' => ['nullable', 'string'],
            'base_stats_json' => ['required', 'array'],
            'base_stats_json.hp' => ['required', 'integer', 'min:0'],
            'base_stats_json.damage' => ['required', 'integer', 'min:0'],
            'base_stats_json.defense' => ['required', 'integer', 'min:0'],
        ];
    }
}
