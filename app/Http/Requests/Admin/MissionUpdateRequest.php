<?php

namespace App\Http\Requests\Admin;

use App\Enums\MissionStatus;
use App\Models\Mission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class MissionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'repeatable' => $this->boolean('repeatable'),
            'base_race_points' => $this->input('base_race_points', 0),
            'xp' => $this->input('xp', 0),
            'gold' => $this->input('gold', 0),
        ]);
    }

    public function rules(): array
    {
        /** @var Mission $mission */
        $mission = $this->route('mission');

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('missions', 'slug')->ignore($mission?->id),
            ],
            'status' => ['required', 'string', Rule::in(array_map(static fn (MissionStatus $status) => $status->value, MissionStatus::cases()))],
            'repeatable' => ['boolean'],
            'base_race_points' => ['required', 'integer', 'min:0'],
            'intro_text' => ['required', 'string'],
            'context_text' => ['nullable', 'string'],
            'final_boss_id' => ['nullable', 'integer', 'exists:final_bosses,id'],
            'xp' => ['required', 'integer', 'min:0'],
            'gold' => ['required', 'integer', 'min:0'],
        ];

        if (Schema::hasTable('items')) {
            $rules['items'] = ['array'];
            $rules['items.*.item_id'] = ['nullable', 'integer', 'exists:items,id'];
            $rules['items.*.qty'] = ['nullable', 'integer', 'min:1'];
        } else {
            $rules['items_json_raw'] = ['nullable', 'string', 'json'];
        }

        return $rules;
    }
}
