<?php

namespace Tests\Feature;

use App\Product\Domain\Models\Product;
use App\Product\Domain\Enums\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function test_can_list_active_products()
    {
        Product::factory()->create(['status' => ProductStatus::ACTIVE]);
        Product::factory()->create(['status' => ProductStatus::ACTIVE]);
        Product::factory()->create(['status' => ProductStatus::ARCHIVED]);

        $response = $this->signedRequest('GET', '/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_list_products_by_ids()
    {
        $p1 = Product::factory()->create(['status' => ProductStatus::ACTIVE]);
        $p2 = Product::factory()->create(['status' => ProductStatus::ACTIVE]);
        $p3 = Product::factory()->create(['status' => ProductStatus::ACTIVE]);

        $response = $this->signedRequest('GET', "/api/products?ids={$p1->id},{$p2->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_list_products_by_uuids()
    {
        $p1 = Product::factory()->create();
        $p2 = Product::factory()->create();

        $response = $this->signedRequest('GET', "/api/products/by-uuid?uuids={$p1->uuid},{$p2->uuid}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_show_product()
    {
        $product = Product::factory()->create();

        $response = $this->signedRequest('GET', "/api/products/{$product->uuid}");

        $response->assertStatus(200)
            ->assertJsonPath('data.uuid', $product->uuid);
    }

    public function test_can_create_product()
    {
        $response = $this->signedRequest('POST', '/api/products', [
            'name' => 'New Product',
            'price' => 150.50,
            'currency' => 'USD',
            'stock_on_hand' => 50,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Product');

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'price' => 150.50,
            'status' => ProductStatus::ACTIVE->value,
        ]);
    }

    public function test_can_archive_product()
    {
        $product = Product::factory()->create(['status' => ProductStatus::ACTIVE]);

        $response = $this->signedRequest('DELETE', "/api/products/{$product->uuid}");

        $response->assertStatus(204);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => ProductStatus::ARCHIVED->value,
        ]);
    }

    public function test_cannot_archive_already_archived_product()
    {
        $product = Product::factory()->create(['status' => ProductStatus::ARCHIVED]);

        $response = $this->signedRequest('DELETE', "/api/products/{$product->uuid}");

        $response->assertStatus(409);
    }
}
