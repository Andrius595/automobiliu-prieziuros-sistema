<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\WriteReviewRequest;
use App\Models\Appointment;
use App\Models\ServiceReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function writeReview(WriteReviewRequest $request, Appointment $appointment): JsonResponse
    {
        $userId = $request->user()->id;
        $review = ServiceReview::updateOrCreate([
            'appointment_id' => $appointment->id,
        ], [
            'user_id' => $userId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json($review);
    }

    public function getReview(Appointment $appointment): JsonResponse
    {
        return response()->json($appointment->review);
    }

}
