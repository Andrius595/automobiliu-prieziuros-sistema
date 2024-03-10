<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
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
        throw new NotImplementedException();
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
