<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Services\Missions\MissionGraphValidator;

class MissionPublishController extends Controller
{
    public function store(Mission $mission, MissionGraphValidator $validator)
    {
        $errors = $validator->validate($mission->load('reward'));

        if (!empty($errors)) {
            return back()->withErrors($errors);
        }

        $mission->update([
            'status' => MissionStatus::Published,
        ]);

        return back()->with('status', 'Mision publicada correctamente.');
    }
}
