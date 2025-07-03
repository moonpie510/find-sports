<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_create_slots_success(): void
    {
        $user = User::factory()->create();
        $slots['slots'] = [
            ['start_time' => '2025-07-04 01:00:00', 'end_time' => '2025-07-04 05:00:00'],
            ['start_time' => '2025-07-05 08:00:00', 'end_time' => '2025-07-05 10:00:00'],
        ];

        $response = $this->post('/api/v1/bookings', $slots, [User::TOKEN => $user->api_token]);

        $response->assertJson([ "success" => true, 'code' => 200]);
    }
}
