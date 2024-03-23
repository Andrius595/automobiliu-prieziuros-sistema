<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public const MILEAGE_TYPE_KILOMETERS = 0;
    public const MILEAGE_TYPE_MILES = 1;

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function completedAppointments(): HasMany
    {
        return $this->appointments()->whereNotNull('completed_at');
    }
}
