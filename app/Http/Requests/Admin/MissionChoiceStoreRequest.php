<?php

namespace App\Http\Requests\Admin;

use App\Models\MissionNode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MissionChoiceStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'choice_text' => ['required', 'string', 'max:255'],
            'outcome_text' => ['nullable', 'string'],
            'difficulty_points' => ['required', 'integer', 'between:0,3'],
            'order' => ['required', 'integer', 'between:1,4'],
            'next_node_id' => ['nullable', 'integer'],
            'goes_to_boss' => ['nullable', 'boolean'],
            'effects_json_raw' => ['nullable', 'string', 'json'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            /** @var MissionNode|null $node */
            $node = $this->route('node');
            if (!$node) {
                return;
            }

            $stepIndex = (int) $node->step_index;
            $goesToBoss = $this->boolean('goes_to_boss');
            $nextNodeId = $this->input('next_node_id');

            if ($stepIndex < 6) {
                if ($goesToBoss) {
                    $validator->errors()->add('goes_to_boss', 'Solo se permite ir al boss en el paso 6.');
                }
                if (!$nextNodeId) {
                    $validator->errors()->add('next_node_id', 'Debes seleccionar el siguiente nodo.');
                    return;
                }

                $nextNode = MissionNode::query()->where('id', $nextNodeId)->first();
                if (!$nextNode || $nextNode->mission_id !== $node->mission_id || (int) $nextNode->step_index !== $stepIndex + 1) {
                    $validator->errors()->add('next_node_id', 'El siguiente nodo debe ser del paso ' . ($stepIndex + 1) . ' y de la misma mision.');
                }
            } else {
                if (!$goesToBoss) {
                    $validator->errors()->add('goes_to_boss', 'Las opciones del paso 6 deben ir al boss final.');
                }
                if ($nextNodeId) {
                    $validator->errors()->add('next_node_id', 'En el paso 6 no se permite siguiente nodo.');
                }
            }
        });
    }
}
