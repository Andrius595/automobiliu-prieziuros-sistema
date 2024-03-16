<?php

namespace App\Http\Controllers;

use App\Actions\Car\CreateNewCar;
use App\Actions\Car\ListCars;
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
        $searchParams['owner_id'] = Auth::id();
        $cars = $listCars->list($searchParams, $perPage, $sortParams, []);

        return response()->json($cars);
    }
}
