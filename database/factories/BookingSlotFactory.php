<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingSlot>
 */
class BookingSlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 day');

        return [
            'booking_id' => Booking::query()->inRandomOrder()->value('id'),
            'start_time' => $startTime,
            'end_time' => $startTime->modify('+60 minutes'),
        ];
    }
}
