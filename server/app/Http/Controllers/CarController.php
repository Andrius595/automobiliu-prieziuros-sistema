<?php

namespace App\Http\Controllers;

use App\Actions\Car\DeletePublicCarHistory;
use App\Actions\Car\ListCars;
use App\Actions\Car\ShareCarHistory;
use App\Actions\Car\UpdateCar;
use App\Config\PermissionsConfig;
use App\Http\Requests\ShareCarHistoryRequest;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nette\NotImplementedException;
use Symfony\Component\HttpFoundation\Response;

class CarController extends Controller
{
    public function index(Request $request, ListCars $listCars): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $searchParams = $request->only([
            'make',
            'model',
            'vin'
        ]);
        $sortParams = $request->only([
            'sortBy',
            'sortDirection',
        ]);
        $cars = $listCars->list($searchParams, $perPage, $sortParams, ['owner']);

        return response()->json($cars);
    }

    public function store(StoreCarRequest $request)
    {
        throw new NotImplementedException();
    }

    public function show(Car $car): JsonResponse
    {
        $user = Auth::user();

        $carBelongsToUser = $car->users()->where('id', $user->id)->exists();
        $userIsAdmin = $user->hasRole(PermissionsConfig::SYSTEM_ADMIN_ROLE);

        $serviceEmployeeDontHaveActiveAppointments = $user->hasRole(PermissionsConfig::SERVICE_EMPLOYEE_ROLE) &&
            !$car->appointments()->where('service_id', $user->service_id)->whereNull('completed_at')->exists();
        if (!$userIsAdmin && !$carBelongsToUser && $serviceEmployeeDontHaveActiveAppointments) {
            throw new AuthorizationException(trans('resources.youre_not_allowed_to_view_this_car'));
        }

        $car->load('owner');

        return response()->json($car);
    }

    public function update(UpdateCarRequest $request, Car $car, UpdateCar $updateCar): JsonResponse
    {
        $updateCar->update($car, $request->validated());

        return response()->json($car);
    }

    public function destroy(Car $car)
    {
        throw new NotImplementedException();
    }

    public function getCarHistory(Car $car): JsonResponse
    {
        $car->load(['completedAppointments.records', 'completedAppointments.service', 'completedAppointments.car']);

        return response()->json($car->completedAppointments);
    }

    public function getCarPublicUrls(Car $car): JsonResponse
    {
        $urls = $car->publicCarHistories;

        return response()->json($urls);
    }

    public function shareCarHistory(ShareCarHistoryRequest $request, Car $car, ShareCarHistory $shareCarHistory): JsonResponse
    {
        $publicHistory = $shareCarHistory->share($car);

        return response()->json($publicHistory, Response::HTTP_CREATED);
    }

    public function deletePublicCarHistory(ShareCarHistoryRequest $request, Car $car, DeletePublicCarHistory $publicCarHistory): JsonResponse
    {
        $publicCarHistory->delete($car);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
