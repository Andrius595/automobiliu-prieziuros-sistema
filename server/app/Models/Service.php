<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Service extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['rating', 'reviews_count'];

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(ServiceReview::class, Appointment::class, 'service_id', 'appointment_id', 'id', 'id');
    }


    protected function rating(): Attribute
    {
        return Attribute::make(
            get: fn () => (float)($this->reviews()->avg('rating') ?? 0)
        );
    }

    protected function reviewsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->reviews()->count()
        );
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function service_categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_service_categories')
            ->withPivot('service_id', 'service_category_id');
    }

}
