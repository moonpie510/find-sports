<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_without_user_token_in_headers(): void
    {
        $response = $this->get('/api/v1/bookings');

        $response->assertJson([ "success" => false, 'code' => 401]);
    }

    public function test_get_slots_success(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();

        $this->assertDatabaseCount('booking_slots', 0);
        BookingSlot::query()->create(['booking_id' => $booking->id, 'start_time' => '2027-07-04 01:00:00', 'end_time' => '2027-07-04 05:00:00']);
        BookingSlot::query()->create(['booking_id' => $booking->id, 'start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00']);
        $this->assertDatabaseCount('booking_slots', 2);

        $response = $this->get('/api/v1/bookings', [User::TOKEN => $user->api_token]);
        $response->assertJson([ "success" => true, 'code' => 200]);
    }

    public function test_create_slots_success(): void
    {
        $user = User::factory()->create();

        $slots['slots'] = [
            ['start_time' => '2027-07-04 01:00:00', 'end_time' => '2027-07-04 05:00:00'],
            ['start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00'],
        ];

        $this->assertDatabaseCount('booking_slots', 0);
        $response = $this->post('/api/v1/bookings', $slots, [User::TOKEN => $user->api_token]);

        $this->assertDatabaseCount('booking_slots', 2);
        $response->assertJson([ "success" => true, 'code' => 200]);
    }

    public function test_create_slots_with_past_date_failed(): void
    {
        $user = User::factory()->create();

        $slots['slots'] = [
            ['start_time' => '2007-07-04 01:00:00', 'end_time' => '2007-07-04 05:00:00'],
            ['start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00'],
        ];

        $this->assertDatabaseCount('booking_slots', 0);
        $response = $this->post('/api/v1/bookings', $slots, [User::TOKEN => $user->api_token]);

        $this->assertDatabaseCount('booking_slots', 0);
        $response->assertJson([ "success" => false, 'code' => 400, 'message' => 'Время начала не может быть меньше текущего времени']);
    }

    public function test_create_slots_with_booked_date_failed()
    {
        $user = User::factory()->create();

        $slots['slots'] = [
            ['start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00'],
        ];

        $this->assertDatabaseCount('booking_slots', 0);
        $response = $this->post('/api/v1/bookings', $slots, [User::TOKEN => $user->api_token]);

        $this->assertDatabaseCount('booking_slots', 1);
        $response->assertJson([ "success" => true, 'code' => 200]);

        $newSlot['slots'] = [
            ['start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00'],
        ];

        $response = $this->post('/api/v1/bookings', $newSlot, [User::TOKEN => $user->api_token]);
        $this->assertDatabaseCount('booking_slots', 1);
        $response->assertJson([ "success" => false, 'code' => 400]);
    }

    public function test_update_slot_success(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $slot = BookingSlot::query()->create(['booking_id' => $booking->id, 'start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00']);
        $interval = ['start_time' => '2027-07-10 08:00:00', 'end_time' => '2027-07-15 10:00:00'];

        $this->assertDatabaseCount('booking_slots', 1);
        $response = $this->patch("/api/v1/bookings/{$booking->id}/slots/{$slot->id}", $interval, [User::TOKEN => $user->api_token]);

        $this->assertDatabaseCount('booking_slots', 1);
        $response->assertJson([ "success" => true, 'code' => 200]);

        $slot->refresh();
        $this->assertEquals($slot->start_time, $interval['start_time']);
        $this->assertEquals($slot->end_time, $interval['end_time']);
    }

    public function test_update_slot_with_wrong_booking_id_failed(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $slot = BookingSlot::query()->create(['booking_id' => $booking->id, 'start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00']);
        $bookingId = $booking->id + 1;
        $interval = ['start_time' => '2027-07-10 08:00:00', 'end_time' => '2027-07-15 10:00:00'];

        $this->assertDatabaseCount('booking_slots', 1);
        $response = $this->patch("/api/v1/bookings/{$bookingId}/slots/{$slot->id}", $interval, [User::TOKEN => $user->api_token]);

        $this->assertDatabaseCount('booking_slots', 1);
        $response->assertJson([ "success" => false, 'code' => 400, 'message' => 'Заказ не найден или принадлежит другому пользователю']);
    }

    public function test_add_slot_success(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $slot = BookingSlot::query()->create(['booking_id' => $booking->id, 'start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00']);
        $interval = ['start_time' => '2027-07-10 08:00:00', 'end_time' => '2027-07-15 10:00:00'];

        $this->assertDatabaseCount('booking_slots', 1);
        $response = $this->post("/api/v1/bookings/{$booking->id}/slots", $interval, [User::TOKEN => $user->api_token]);

        $response->assertJson([ "success" => true, 'code' => 200]);
        $this->assertDatabaseCount('booking_slots', 2);
    }

    public function test_add_slot_with_booked_date_failed(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $slot = BookingSlot::query()->create(['booking_id' => $booking->id, 'start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00']);
        $interval = ['start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00'];

        $this->assertDatabaseCount('booking_slots', 1);
        $response = $this->post("/api/v1/bookings/{$booking->id}/slots", $interval, [User::TOKEN => $user->api_token]);

        $response->assertJson([ "success" => false, 'code' => 400]);
        $this->assertDatabaseCount('booking_slots', 1);
    }

    public function test_delete_slot_success(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $slot = BookingSlot::query()->create(['booking_id' => $booking->id, 'start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00']);

        $this->assertDatabaseCount('booking_slots', 1);
        $this->assertDatabaseCount('bookings', 1);

        $response = $this->delete("/api/v1/bookings/{$booking->id}", [], [User::TOKEN => $user->api_token]);

        $response->assertJson([ "success" => true, 'code' => 200]);
        $this->assertDatabaseCount('booking_slots', 0);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_delete_slot_by_wrong_user(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $booking = Booking::query()->create(['user_id' => $user->id]);
        $slot = BookingSlot::query()->create(['booking_id' => $booking->id, 'start_time' => '2027-07-05 08:00:00', 'end_time' => '2027-07-05 10:00:00']);

        $this->assertDatabaseCount('booking_slots', 1);
        $this->assertDatabaseCount('bookings', 1);

        $response = $this->delete("/api/v1/bookings/{$booking->id}", [], [User::TOKEN => $anotherUser->api_token]);

        $response->assertJson([ "success" => false, 'code' => 400, 'message' => 'Заказ не найден или принадлежит другому пользователю']);
        $this->assertDatabaseCount('booking_slots', 1);
        $this->assertDatabaseCount('bookings', 1);
    }
}
