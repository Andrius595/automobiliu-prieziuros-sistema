<?php

namespace App\Http\Controllers;

use App\Actions\ServiceCategory\CreateNewServiceCategory;
use App\Actions\ServiceCategory\ListServiceCategories;
use App\Actions\ServiceCategory\UpdateServiceCategory;
use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index(Request $request, ListServiceCategories $listServiceCategories): JsonResponse
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
        return response()->json($listServiceCategories->list($searchParams, $perPage, $sortParams));
    }

    public function indexForSelect(): JsonResponse
    {
        return response()->json(ServiceCategory::orderBy('name')->get(['id', 'name']));
    }

    public function store(StoreServiceCategoryRequest $request, CreateNewServiceCategory $newServiceCategory): JsonResponse
    {
        $serviceCategory = $newServiceCategory->create($request->validated());

        return response()->json($serviceCategory, 201);
    }

    public function show(ServiceCategory $serviceCategory): JsonResponse
    {
        return response()->json($serviceCategory);
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory, UpdateServiceCategory $updateServiceCategory): JsonResponse
    {
        $updateServiceCategory->update($serviceCategory, $request->validated());

        return response()->json($serviceCategory);
    }

    public function destroy(ServiceCategory $serviceCategory): JsonResponse
    {
        $serviceCategory->delete();

        return response()->json(null, 204);
    }
}
