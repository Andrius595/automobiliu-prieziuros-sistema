<?php

namespace App\Http\Controllers;

use App\Config\PermissionsConfig;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Nette\NotImplementedException;

class CarController extends Controller
{
    public function index()
    {
        throw new NotImplementedException();
    }

    public function store(StoreCarRequest $request)
    {
        throw new NotImplementedException();
    }

    public function show(Car $car)
    {
        $user = Auth::user();

        $clientIsNotOwner = $user->hasRole(PermissionsConfig::CLIENT_ROLE) && $user->id !== $car->owner_id;
        $serviceEmployeeNotActiveAppointments = $user->hasRole(PermissionsConfig::SERVICE_EMPLOYEE_ROLE) && !$car->appointments()->where('service_id', $user->service_id)->whereNull('completed_at')->exists();
        if ($clientIsNotOwner || $serviceEmployeeNotActiveAppointments) {
            throw new AuthorizationException('You are not allowed to view this car');
        }

        return response()->json($car);
    }

    public function update(UpdateCarRequest $request, Car $car)
    {
        throw new NotImplementedException();
    }

    public function destroy(Car $car)
    {
        throw new NotImplementedException();
    }
}
