<?php

namespace Tests\E2E;

use Illuminate\Support\Str;
use Tests\TestCase;

class FullFlowTest extends TestCase
{
    /**
     * ✅ Full E2E Flow: Register -> Login -> Create Product -> Create Order
     * This test interacts with REAL services running in Docker.
     */
    public function test_full_ecommerce_flow()
    {
        $email = 'e2e-'.Str::random(8).'@example.com';
        $password = 'password123';
        $token = null;
        $productUuid = null;
        $orderUuid = null;

        try {
            // 1. Register User via API Gateway
            $registerResponse = $this->postJson('/api/auth/register', [
                'name' => 'E2E User',
                'email' => $email,
                'password' => $password,
            ]);
            $registerResponse->assertStatus(201);

            // 2. Login User via API Gateway to get JWT
            $loginResponse = $this->postJson('/api/auth/login', [
                'email' => $email,
                'password' => $password,
            ]);
            $loginResponse->assertStatus(200);
            $token = $loginResponse->json('access_token');
            $this->assertNotEmpty($token);

            // 3. Create Product (via Gateway)
            $productUuid = (string) Str::uuid();
            $productResponse = $this->withHeader('Authorization', 'Bearer '.$token)
                ->postJson('/api/products', [
                    'name' => 'E2E Product',
                    'price' => 100,
                    'currency' => 'USD',
                    'stock_on_hand' => 50,
                    'uuid' => $productUuid,
                ]);
            $productResponse->assertStatus(201);

            // 4. Create Order (via Gateway)
            $orderResponse = $this->withHeader('Authorization', 'Bearer '.$token)
                ->postJson('/api/orders', [
                    'items' => [
                        [
                            'product_uuid' => $productUuid,
                            'quantity' => 2,
                        ],
                    ],
                ]);
            $orderResponse->assertStatus(201);
            $orderUuid = $orderResponse->json('data.id');

            // 5. Verify Stock was Reserved (RabbitMQ Async)
            // Give RabbitMQ time to process (Order -> Outbox -> Product Worker)
            sleep(5);

            $checkProductResponse = $this->withHeader('Authorization', 'Bearer '.$token)
                ->getJson('/api/products/'.$productUuid);

            $checkProductResponse->assertStatus(200);
            $this->assertEquals(2, $checkProductResponse->json('data.stock_reserved'), 'Stock should be reserved in product-service');
            $this->assertEquals(48, $checkProductResponse->json('data.stock_available'), 'Available stock should decrease');

        } finally {
            // Cleanup - removing data from real DBs
            if ($token) {
                if ($orderUuid) {
                    // We can't actually DELETE orders via Gateway if there's no route,
                    // but we can CANCEL it if it exists.
                    $this->withHeader('Authorization', 'Bearer '.$token)
                        ->deleteJson('/api/orders/'.$orderUuid);
                }

                if ($productUuid) {
                    $this->withHeader('Authorization', 'Bearer '.$token)
                        ->deleteJson('/api/products/'.$productUuid);
                }

                // Note: Auth service usually doesn't have a delete user endpoint in this project.
                // We'll leave the user for now or add a cleanup method if needed.
            }
        }
    }
}
