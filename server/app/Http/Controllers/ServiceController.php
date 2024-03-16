<?php

namespace App\Http\Controllers;

use App\Actions\Appointment\CompleteAppointment;
use App\Actions\Appointment\CreateNewAppointment;
use App\Actions\Appointment\ListAppointments;
use App\Actions\Car\CreateNewCar;
use App\Actions\Record\CreateNewRecord;
use App\Actions\Record\ListRecords;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\Record;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }

    public function getRegistrations(Request $request, ListAppointments $listAppointments): JsonResponse
    {
        $perPage = $request->input('perPage');
        $searchParams = $request->only([
            'make',
            'model',
            'vin'
        ]);
        $sortParams = $request->only([
            'sortBy',
            'sortDirection',
        ]);
        $relations = ['car.owner'];
        $searchParams['registrations'] = true;
        $searchParams['service_id'] = Auth::user()->service_id;
        $appointments = $listAppointments->list($searchParams, $perPage, $sortParams, $relations);

        return response()->json($appointments);
    }

    public function confirmRegistration(Appointment $appointment): JsonResponse
    {
        $user = Auth::user();

        if ($user->service_id !== $appointment->service_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $appointment->update([
            'confirmed_at' => now(),
        ]);

        return response()->json($appointment, Response::HTTP_OK);
    }

    public function getActiveAppointments(Request $request, ListAppointments $listAppointments): JsonResponse
    {
        $perPage = $request->input('perPage');
        $searchParams = $request->only([
            'make',
            'model',
            'vin'
        ]);
        $sortParams = $request->only([
            'sortBy',
            'sortDirection',
        ]);
        $relations = ['car.owner'];
        $searchParams['active'] = true;
        $searchParams['service_id'] = Auth::user()->service_id;
        $appointments = $listAppointments->list($searchParams, $perPage, $sortParams, $relations);

        return response()->json($appointments);
    }

    public function getAppointment(Appointment $appointment): JsonResponse
    {
        $user = Auth::user();

        if ($user->service_id !== $appointment->service_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $appointment->load(['car.owner', 'service', 'records']);

        return response()->json($appointment);
    }

    public function getAppointmentRecords(Request $request, Appointment $appointment, ListRecords $listRecords): JsonResponse
    {
        $user = Auth::user();

        if ($user->service_id !== $appointment->service_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $perPage = $request->input('perPage');
        $searchParams = $request->only([
            'make',
            'model',
            'vin'
        ]);
        $sortParams = $request->only([
            'sortBy',
            'sortDirection',
        ]);
        $searchParams['appointment_id'] = $appointment->id;
        $records = $listRecords->list($searchParams, $perPage, $sortParams);

        return response()->json($records);
    }

    public function completeAppointment(Request $request, Appointment $appointment, CompleteAppointment $completeAppointment): JsonResponse
    {
        $user = Auth::user();

        if ($appointment->can_be_modified && $user->service_id !== $appointment->service_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $completeAppointment->complete($appointment);

        return response()->json($appointment, Response::HTTP_OK);
    }

    public function getCarByVin($vin): JsonResponse
    {
        $car = Car::where('vin', $vin)->firstOrFail();

        $car->current_mileage = $car->appointments()->whereNotNull('completed_at')->orderBy('completed_at', 'desc')->first()->current_mileage ?? 0;

        return response()->json($car);
    }

    public function createAppointment(Request $request, CreateNewAppointment $newAppointment, CreateNewCar $newCar): JsonResponse
    {
        $data = $request->all();
        $data['service_id'] = Auth::user()->service_id;
        $data['car_id'] = $data['car_id'] ?? $newCar->create($data)->id;
        $data['confirmed_at'] = now();

        $appointment = $newAppointment->create($data);


        return response()->json($appointment, Response::HTTP_CREATED);
    }

    public function createRecord(Request $request, Appointment $appointment, CreateNewRecord $newRecord): JsonResponse
    {
        $user = Auth::user();

        if ($appointment->can_be_modified && $user->service_id !== $appointment->service_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $data = $request->all();
        $data['appointment_id'] = $appointment->id;

        $record = $newRecord->create($data);

        return response()->json($record, Response::HTTP_CREATED);
    }

    public function deleteRecord(Record $record): JsonResponse
    {
        $appointment = $record->appointment;
        $user = Auth::user();

        if ($appointment->can_be_modified && $user->service_id !== $appointment->service_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $record->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getRecord(Record $record): JsonResponse
    {
        $appointment = $record->appointment;
        $user = Auth::user();

        if ($user->service_id !== $appointment->service_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json($record);
    }

    public function updateRecord(Request $request, Record $record): JsonResponse
    {
        $appointment = $record->appointment;
        $user = Auth::user();

        if ($appointment->can_be_modified && $user->service_id !== $appointment->service_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $record->update($request->all());

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
