<?php

namespace App\Actions\User;

use App\Actions\ListsRecords;
use App\Models\Service;
use App\Models\User;

class ListUsers extends ListsRecords
{
    public string $model = User::class;

    public function searchQuery($searchParams): static
    {
        if (isset($searchParams['role'])) {
            if (!is_array($searchParams['role'])) {
                $searchParams['role'] = [$searchParams['role']];
            }
        }
        foreach ($searchParams as $key => $searchParam) {
            match($key) {
                'role' => $this->query->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.*')
                    ->whereIn('roles.name', $searchParam),
                'service_id' => $this->query->where('service_id', $searchParam),
                default => $this->query->where($key, 'like', "%$searchParam%"),
            };
        }

        return $this;
    }

    public function sortQuery($sortParams): static
    {
        if (isset($sortParams['sortBy'])) {
            match ($sortParams['sortBy']) {
                'roles' => $this->query->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.*')
                    ->orderBy('roles.name', $sortParams['sortDirection'] ?? 'asc'),
                default => $this->query->orderBy($sortParams['sortBy'], $sortParams['sortDirection'] ?? 'asc'),
            };        }

        return $this;
    }
}
