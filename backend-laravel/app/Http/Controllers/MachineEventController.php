<?php

namespace App\Http\Controllers;

use App\Models\MachineEvent;

class MachineEventController extends Controller
{
    // Último evento (tempo real)
    public function latest()
    {
        return response()->json(
            MachineEvent::latest('event_time')->first()
        );
    }

    // Lista últimos eventos
    public function index()
    {
        return response()->json(
            MachineEvent::orderBy('event_time', 'desc')
                ->limit(50)
                ->get()
        );
    }

    // OEE médio (ex: últimas 1h)
    public function oee()
    {
        $data = MachineEvent::where('event_time', '>=', now()->subHour())->get();

        if ($data->isEmpty()) {
            return response()->json(['oee' => 0]);
        }

        return response()->json([
            'oee' => round($data->avg('oee'), 2),
            'availability' => round($data->avg('availability'), 2),
            'performance' => round($data->avg('performance'), 2),
            'quality' => round($data->avg('quality'), 2),
        ]);
    }
}