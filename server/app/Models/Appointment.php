<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(ServiceReview::class);
    }

    protected function canBeModified(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['completed_at'] === null,
        );
    }
}
