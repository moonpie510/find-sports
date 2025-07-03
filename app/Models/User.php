<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * Название header для токена, который нужно передавать в headers для авторизации.
     */
    public const string TOKEN = 'User-Token';

    protected $hidden = ['api_token'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function slots(): HasManyThrough
    {
        return $this->hasManyThrough(BookingSlot::class, Booking::class);
    }
}
