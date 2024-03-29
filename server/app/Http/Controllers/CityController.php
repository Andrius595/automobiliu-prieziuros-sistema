<?php

namespace App\Http\Controllers;

use App\Actions\City\CreateNewCity;
use App\Actions\City\ListCities;
use App\Actions\City\UpdateCity;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request, ListCities $listCities): JsonResponse
    {
        $perPage = $request->input('perPage', 10);
        $sortParams = $request->only(['sortBy', 'sortDirection']);
        $searchParams = $request->only(['name']);

        return response()->json($listCities->list($searchParams, $perPage, $sortParams));
    }

    public function indexForSelect(): JsonResponse
    {
        return response()->json(City::all(['id', 'name']));
    }

    public function show(City $city): JsonResponse
    {
        return response()->json($city);
    }

    public function store(StoreCityRequest $request, CreateNewCity $newCity): JsonResponse
    {
        $city = $newCity->create($request->validated());

        return response()->json($city);
    }


    public function update(UpdateCityRequest $request, City $city, UpdateCity $updateCity): JsonResponse
    {
        $updateCity->update($city, $request->validated());

        return response()->json($city);
    }

    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return response()->json(null, 204);
    }
}
