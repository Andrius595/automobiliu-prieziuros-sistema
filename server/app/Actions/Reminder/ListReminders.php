<?php

namespace App\Actions\Reminder;

use App\Actions\ListsRecords;
use App\Models\Reminder;

class ListReminders extends ListsRecords
{
    public string $model = Reminder::class;

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
