<?php

namespace App\Http\Controllers;

use App\Actions\Car\CreateNewCar;
use App\Actions\Car\ListCars;
use App\Actions\Car\ShareCar;
use App\Actions\Car\ShareCarHistory;
use App\Actions\Car\TransferCar;
use App\Actions\Car\UpdateCar;
use App\Config\PermissionsConfig;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function registerNewCar(Request $request, CreateNewCar $createNewCar): JsonResponse
    {
        $data = $request->all();
        $data['owner_id'] = Auth::id();
        $car = $createNewCar->create($data);

        return response()->json($car, Response::HTTP_CREATED);
    }

    public function getMyCars(Request $request, ListCars $listCars): JsonResponse
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
        $searchParams['owner_id'] = Auth::id();
        $cars = $listCars->list($searchParams, $perPage, $sortParams, []);

        return response()->json($cars);
    }

    public function removeCar(Car $car, UpdateCar $updateCar): JsonResponse
    {
        $data = [
            'owner_id' => null,
            'owner_confirmed_at' => null,
        ];

        $updateCar->update($car, $data);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function shareCar(Request $request, Car $car, ShareCar $shareCar): JsonResponse
    {
        $user = Auth::user();
        $email = $request->input('email');
        if (($user->id !== $car->owner_id && !$user->hasRole(PermissionsConfig::SYSTEM_ADMIN_ROLE))
            || $car->owner->email === $request->input('email')
        ) {
            return response()->json([
                'message' => 'Negalite atlikti šio veiksmo.'
            ], Response::HTTP_FORBIDDEN);
        }

        $shareCar->share($car, $email);

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function transferCar(Request $request, Car $car, TransferCar $transferCar): JsonResponse
    {
        $user = Auth::user();
        $email = $request->input('email');
        if (($user->id !== $car->owner_id && !$user->hasRole(PermissionsConfig::SYSTEM_ADMIN_ROLE))
            || $car->owner->email === $request->input('email')
        ) {
            return response()->json([
                'message' => 'Negalite atlikti šio veiksmo.'
            ], Response::HTTP_FORBIDDEN);
        }

        $transferCar->transfer($car, $email);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function shareCarHistory(Car $car, ShareCarHistory $shareCarHistory): JsonResponse
    {
        $user = Auth::user();
        if ($user->id !== $car->owner_id && !$user->hasRole(PermissionsConfig::SYSTEM_ADMIN_ROLE)) {
            return response()->json([
                'message' => 'Negalite atlikti šio veiksmo.'
            ], Response::HTTP_FORBIDDEN);
        }

        $publicHistory = $shareCarHistory->share($car);

        return response()->json($publicHistory, Response::HTTP_CREATED);
    }

    public function deletePublicCarHistory()
    {

    }
}
