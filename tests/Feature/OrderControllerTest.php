<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_store(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $token = JWTAuth::fromUser($user);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson('/api/v1/orders');
        $response->assertStatus(200);
    }

    public function test_()
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $token = JWTAuth::fromUser($user);

        Order::create([
            'user_id' => $user->id,
            'order_number' => 'SDSD654SD',
            'total' => 50,
            'status' => 'delivered',
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
            'shipping_cost' => 60,
            'shipping_address' => 'gulshan'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson('/api/v1/orders/history');
        $response->assertStatus(200);
    }
}
