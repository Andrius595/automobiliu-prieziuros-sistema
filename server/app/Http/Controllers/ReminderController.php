<?php

namespace App\Http\Controllers;

use App\Actions\Reminder\CreateNewReminder;
use App\Actions\Reminder\ListReminders;
use App\Actions\Reminder\UpdateReminder;
    use App\Http\Requests\UpdateReminderRequest;
use App\Models\Car;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReminderController extends Controller
{
    public function store(Request $request, Car $car, CreateNewReminder $newReminder): JsonResponse
    {
        $request->merge([
            'car_id' => $car->id,
        ]);
        $reminder = $newReminder->create($request->all());

        return response()->json($reminder, Response::HTTP_CREATED);
    }

    public function show(Reminder $reminder): JsonResponse
    {
        return response()->json($reminder);
    }

    public function update(Request $request, Reminder $reminder, UpdateReminder $updateReminder): JsonResponse
    {
        $updateReminder->update($reminder, $request->all());

        return response()->json($reminder);
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getCarReminders(Request $request, Car $car, ListReminders $listReminders): JsonResponse
    {
        $perPage = $request->input('perPage');
        $searchParams = $request->only([
            'title',
            'description',
        ]);
        $sortParams = $request->only([
            'sortBy',
            'sortDirection',
        ]);
        $searchParams['car_id'] = $car->id;

        $reminders = $listReminders->list($searchParams, $perPage, $sortParams);

        return response()->json($reminders);
    }
}
