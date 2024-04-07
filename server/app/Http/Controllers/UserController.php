<?php

namespace App\Http\Controllers;

use App\Actions\Car\CreateNewCar;
use App\Actions\Car\DeletePublicCarHistory;
use App\Actions\Car\ListCars;
use App\Actions\Car\RemoveCarFromUser;
use App\Actions\Car\ShareCar;
use App\Actions\Car\ShareCarHistory;
use App\Actions\Car\TransferCar;
use App\Actions\Car\UpdateCar;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\User\ListUsers;
use App\Config\PermissionsConfig;
use App\Http\Requests\RemoveCarFromUserRequest;
use App\Http\Requests\ShareCarHistoryRequest;
use App\Http\Requests\ShareCarRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Car;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(Request $request, ListUsers $listUsers): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $searchParams = $request->only([
            'first_name',
            'last_name',
            'email',
            'role',
        ]);
        $sortParams = $request->only([
            'sortBy',
            'sortDirection',
        ]);
        $searchParams['role'] = [PermissionsConfig::CLIENT_ROLE, PermissionsConfig::SYSTEM_ADMIN_ROLE];
        $users = $listUsers->list($searchParams, $perPage, $sortParams, ['roles']);

        return response()->json($users);
    }
    public function show(User $user): JsonResponse
    {
        $user->role = $user->roles()->first()->name;

        return response()->json($user);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'Negalite ištrinti savęs.'
            ], Response::HTTP_FORBIDDEN);
        }
        $cars = $user->cars;

        foreach ($cars as $car) {
            $car->update([
                'owner_id' => null,
                'owner_confirmed_at' => null,
            ]);
        }
        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function store(StoreUserRequest $request, CreateNewUser $newUser): JsonResponse
    {
        $data = $request->all();
        $user = $newUser->create($data);

        event(new Registered($user));

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function indexForSelect(): JsonResponse
    {
        $users = User::all(['id', 'first_name', 'last_name']);

        return response()->json($users);
    }
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
        $searchParams['user_id'] = Auth::id();
        $cars = $listCars->list($searchParams, $perPage, $sortParams);


        return response()->json($cars);
    }

    public function removeCar(RemoveCarFromUserRequest $request, Car $car, RemoveCarFromUser $removeCarFromUser): JsonResponse
    {
        $removeCarFromUser->remove($car, Auth::id());

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function shareCar(ShareCarRequest $request, Car $car, ShareCar $shareCar): JsonResponse
    {
        $email = $request->input('email');
        if ($car->users()->where('email', $email)->exists()){
            return response()->json([
                'message' => trans('resources.user_already_has_access_to_this_car')
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
}
