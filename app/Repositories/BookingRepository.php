<?php

namespace App\Repositories;

use App\Classes\Interval;
use App\Classes\IntervalCollection;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookingRepository
{
    /**
     * Формирует query для booking_slots.
     */
    private function getBookingSlotQuery(
        ?User $user = null,
        ?string $orderBy = null,
        ?int $excludedSlotId = null,
        ?int $slotId = null,
        ?int $bookingId = null
    ): Builder
    {
        $query = BookingSlot::query();

        if ($orderBy !== null) {
            $query->orderBy($orderBy);
        }

        if ($user !== null) {
            $query->whereHas('bookings', fn($q) => $q->where('user_id', $user->id));
        }

        if ($excludedSlotId !== null) {
            $query->where('id', '!=', $excludedSlotId);
        }

        if ($slotId !== null) {
            $query->where('id', $slotId);
        }

        if ($bookingId !== null) {
            $query->where('booking_id', $bookingId);
        }

        return $query;
    }

    /**
     * Формирует query для bookings.
     */
    private function getBookingQuery(
        ?int $id = null
    ): Builder
    {
        $query = Booking::query();

        if ($id !== null) {
            $query->where('id', $id);
        }

        return $query;
    }

    /**
     * Получить заказ по id.
     */
    public function getBookingById(int $id): ?Booking
    {
        return $this->getBookingQuery(id: $id)->first();
    }

    /**
     * Получить список бронирований пользователя.
     */
    public function getSlotsByUser(User $user): Collection
    {
        return $this->getBookingSlotQuery(user: $user, orderBy: 'start_time')->get();
    }

    /**
     * Получить слот по id.
     */
    public function getSlotById(int $slotId, ?int $bookingId = null): ?BookingSlot
    {
        return $this->getBookingSlotQuery(slotId: $slotId, bookingId: $bookingId)->first();
    }

    /**
     * Создать новые слоты бронирования.
     */
    public function createSlots(User $user, IntervalCollection $intervals): void
    {
        DB::transaction(function () use ($user, $intervals) {
            foreach ($intervals->intervals as $interval) {
                /** @var Interval $interval */

                if (!$this->isTimeAvailable($interval)) {
                    throw new \Exception("Время: с {$interval->start->format('Y-m-d H:i')} по {$interval->end->format('Y-m-d H:i')} уже занято");
                }

                $booking = $user->bookings()->create();
                $booking->slots()->create(['start_time' => $interval->start, 'end_time' => $interval->end]);
            }
        });
    }

    /**
     * Обновить слот бронирования.
     */
    public function updateSlot(BookingSlot $slot, Interval $interval): void
    {
        if (!$this->isTimeAvailable($interval, $slot->id)) {
            throw new \Exception("Время: с {$interval->start->format('Y-m-d H:i')} по {$interval->end->format('Y-m-d H:i')} уже занято");
        }

        $slot->update(['start_time' => $interval->start, 'end_time' => $interval->end]);
    }

    /**
     * Добавить новый слот к существующему заказу.
     */
    public function addSlot(Booking $booking, Interval $interval): void
    {
        if (!$this->isTimeAvailable($interval)) {
            throw new \Exception("Время: с {$interval->start->format('Y-m-d H:i')} по {$interval->end->format('Y-m-d H:i')} уже занято");
        }

        $booking->slots()->create(['start_time' => $interval->start, 'end_time' => $interval->end]);
    }

    /**
     * Удалить заказ и его слоты.
     */
    public function deleteBooking(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking->slots()->delete();
            $booking->delete();
        });
    }

    /**
     * Проверить, свободно ли время, нет ли пересечений.
     */
    public function isTimeAvailable(Interval $interval, ?int $excludedSlotId = null): bool
    {
        $query = $this->getBookingSlotQuery(excludedSlotId: $excludedSlotId);

        $query->where(function($query) use ($interval) {
            // Существующее бронирование начинается внутри нового интервала
            $query->where('start_time', '>=', $interval->start)
                ->where('start_time', '<', $interval->end);
        })->orWhere(function($query) use ($interval) {
            // Существующее бронирование заканчивается внутри нового интервала
            $query->where('end_time', '>', $interval->start)
                ->where('end_time', '<=', $interval->end);
        })->orWhere(function($query) use ($interval) {
            // Существующее бронирование полностью содержит новый интервал
            $query->where('start_time', '<=', $interval->start)
                ->where('end_time', '>=', $interval->end);
        });

        return !$query->exists();
    }
}
