<?php

namespace App\Actions\Appointment;

use App\Models\Appointment;

class ListAppointments
{
    public function list(array $searchParams = [], int $perPage = 10,  $sortParams = [], $relations = [])
    {
        $query = Appointment::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($searchParams as $key => $searchParam) {
            match ($key) {
                'owner_id' => $query->where('owner_id', $searchParam),
                'owner_first_name' => $query->whereHas('car.owner', function ($q) use ($searchParam) {
                    $q->where('first_name', 'like', "%$searchParam%");
                }),
                'registrations' => (bool)$searchParams['registrations'] === true ? $query->whereNull('confirmed_at') : $query,
                'active' => (bool)$searchParams['active'] === true ? $query->whereNull('completed_at') : $query,
                default => $query->where($key, 'like', "%$searchParam%"),
            };
        }

        if (isset($sortParams['sortBy'])) {
            match ($sortParams['sortBy']) {
                'owner' => $query->join('users', 'cars.owner_id', '=', 'users.id')
                    ->orderBy('users.first_name', $sortParams['sortDirection']),
                default => $query->orderBy($sortParams['sortBy'], $sortParams['sortDirection'] ?? 'asc'),
            };
        }

        return $query->paginate($perPage);
    }
}
