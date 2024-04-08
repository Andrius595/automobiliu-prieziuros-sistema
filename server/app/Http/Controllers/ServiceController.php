<?php

namespace App\Http\Controllers;

use App\Actions\Appointment\CompleteAppointment;
use App\Actions\Appointment\CreateNewAppointment;
use App\Actions\Appointment\ListAppointments;
use App\Actions\Car\CreateNewCar;
use App\Actions\Record\CreateNewRecord;
use App\Actions\Record\ListRecords;
use App\Actions\Service\CreateEmployee;
use App\Actions\Service\ListServices;
use App\Actions\Service\UpdateEmployee;
use App\Actions\Service\UpdateService;
use App\Actions\User\ListUsers;
use App\Config\PermissionsConfig;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Appointment;
use App\Models\Car;
use App\Models\Record;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    public function index(Request $request, ListServices $listServices): JsonResponse
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

        $services = $listServices->list($searchParams, $perPage, $sortParams);

        return response()->json($services);
    }

    public function store(StoreServiceRequest $request, CreateEmployee $newEmployee): JsonResponse
    {
        $data = $request->validated();

        $serviceData = [
            'title' => $data['title'],
        ];
        $service = Service::create($serviceData);

        $employeeData = [
            ...$data,
            'service_id' => $service->id,
            'role' => PermissionsConfig::SERVICE_ADMIN_ROLE,
        ];
        $user = $newEmployee->create($employeeData);

        return response()->json([
            'service' => $service,
            'user' => $user,
        ], Response::HTTP_CREATED);
    }

    public function show(Service $service): JsonResponse
    {
        $service->service_categories_ids = $service->service_categories()->pluck('service_categories.id');

        return response()->json($service);
    }

    public function update(UpdateServiceRequest $request, Service $service, UpdateService $updateService): JsonResponse
    {
        $data = $request->validated();
        $data['image'] = $request->file('image')[0] ?? null;
        $updateService->update($service, $data);

        return response()->json($service);
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

    public function getCompletedAppointments(Request $request, ListAppointments $listAppointments): JsonResponse
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
        $searchParams['completed'] = true;
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

    public function registerForAppointment(Request $request, Service $service, CreateNewAppointment $newAppointment): JsonResponse
    {
        $user = Auth::user();
        $carId = $request->input('car_id');
        $car = Car::findOrFail($carId);

        if ($user->id !== $car->owner_id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $appointment = $newAppointment->create([
            'service_id' => $service->id,
            'car_id' => $car->id,
            'current_mileage' => $car->appointments()->whereNotNull('completed_at')->orderBy('completed_at', 'desc')->first()->current_mileage ?? 1,
            'mileage_type' => $car->mileage_type,
        ]);

        return response()->json($appointment, Response::HTTP_CREATED);
    }

    public function getEmployees(Request $request, Service $service, ListUsers $listEmployees): JsonResponse
    {
        $perPage = $request->input('perPage');
        $searchParams = $request->only([
            'name',
            'email',
        ]);
        $sortParams = $request->only([
            'sortBy',
            'sortDirection',
        ]);
        $searchParams['service_id'] = $service->id;
        $employees = $listEmployees->list($searchParams, $perPage, $sortParams, ['roles']);

        return response()->json($employees);
    }

    public function getEmployee(Service $service, User $user): JsonResponse
    {
        if ($user->service_id !== $service->id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }
        $user->role = $user->roles()->first()->name;

        return response()->json($user);
    }

    public function createEmployee(Request $request, Service $service, CreateEmployee $createEmployee): JsonResponse
    {
        $data = $request->all();
        $data['service_id'] = $service->id;

        $employee = $createEmployee->create($data);

        return response()->json($employee, Response::HTTP_CREATED);
    }

    public function deleteEmployee(Request $request, Service $service, User $user): JsonResponse
    {
        if ($user->service_id !== $service->id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function updateEmployee(Request $request, Service $service, User $user, UpdateEmployee $updateEmployee): JsonResponse
    {
        if ($user->service_id !== $service->id) {
            return response()->json(['message' => 'Negalite atlikti šio veiksmo'], Response::HTTP_UNAUTHORIZED);
        }

        $updateEmployee->update($user, $request->all());

        return response()->json($user);
    }
}
