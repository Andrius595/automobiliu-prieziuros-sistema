<?php

namespace App\Http\Controllers;

use App\Models\PublicCarHistory;
use Illuminate\Http\JsonResponse;

class PublicCarHistoryController extends Controller
{
    public function showBySlug($slug): JsonResponse
    {
        $history = PublicCarHistory::with('car.appointments.car')
            ->where('slug', $slug)->firstOrFail();

        $car = $history->car;
        $car->load(['completedAppointments.records', 'completedAppointments.service', 'completedAppointments.car']);

        return response()->json($car->completedAppointments);
    }
}
