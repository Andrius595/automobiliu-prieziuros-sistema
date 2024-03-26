<?php

namespace App\Actions\Appointment;

use App\Actions\ListsRecords;
use App\Models\Appointment;

class ListAppointments extends ListsRecords
{
    public string $model = Appointment::class;

    public function searchQuery($searchParams): static
    {
        foreach ($searchParams as $key => $searchParam) {
            match ($key) {
                'owner_id' => $this->query->where('owner_id', $searchParam),
                'owner_first_name' => $this->query->whereHas('car.owner', function ($q) use ($searchParam) {
                    $q->where('first_name', 'like', "%$searchParam%");
                }),
                'registrations' => (bool)$searchParams['registrations'] === true ? $this->query->whereNull('confirmed_at') : $this->query,
                'active' => (bool)$searchParams['active'] === true ? $this->query->whereNull('completed_at') : $this->query,
                'completed' => (bool)$searchParams['completed'] === true ? $this->query->whereNotNull('completed_at') : $this->query,
                default => $this->query->where($key, 'like', "%$searchParam%"),
            };
        }

        return $this;
    }

    public function sortQuery($sortParams): static
    {
        if (isset($sortParams['sortBy'])) {
            match ($sortParams['sortBy']) {
                'owner' => $this->query->join('users', 'cars.owner_id', '=', 'users.id')
                    ->orderBy('users.first_name', $sortParams['sortDirection']),
                default => $this->query->orderBy($sortParams['sortBy'], $sortParams['sortDirection'] ?? 'asc'),
            };
        }

        return $this;
    }
}
