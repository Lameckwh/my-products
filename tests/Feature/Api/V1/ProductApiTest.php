<?php

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/api/v1/products';

    // ── Index ────────────────────────────────────────────────────────────

    public function test_index_returns_paginated_products(): void
    {
        Product::factory(3)->create();

        $response = $this->getJson(self::BASE);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'name', 'sku', 'price', 'description', 'created_at', 'updated_at']],
                'meta' => ['current_page', 'total'],
            ]);
    }

    // ── Store ────────────────────────────────────────────────────────────

    public function test_store_creates_product_and_returns_201(): void
    {
        $payload = [
            'name' => 'Red Hat Enterprise Linux',
            'sku' => 'RHEL-001',
            'price' => 299.99,
            'description' => 'Enterprise Linux subscription.',
        ];

        $response = $this->postJson(self::BASE, $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.sku', 'RHEL-001')
            ->assertJsonPath('data.name', 'Red Hat Enterprise Linux');

        $this->assertDatabaseHas('products', ['sku' => 'RHEL-001']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson(self::BASE, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'sku', 'price']);
    }

    public function test_store_validates_duplicate_sku(): void
    {
        Product::factory()->create(['sku' => 'DUPE-001']);

        $response = $this->postJson(self::BASE, [
            'name' => 'Another',
            'sku' => 'DUPE-001',
            'price' => 49.99,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('sku');
    }

    public function test_store_validates_negative_price(): void
    {
        $response = $this->postJson(self::BASE, [
            'name' => 'Bad Price',
            'sku' => 'BAD-001',
            'price' => -1,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('price');
    }

    // ── Show ─────────────────────────────────────────────────────────────

    public function test_show_returns_product(): void
    {
        $product = Product::factory()->create(['name' => 'OpenShift']);

        $response = $this->getJson(self::BASE."/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.name', 'OpenShift')
            ->assertJsonPath('data.sku', $product->sku);
    }

    public function test_show_returns_404_for_missing_product(): void
    {
        $response = $this->getJson(self::BASE.'/9999');

        $response->assertStatus(404);
    }

    // ── Update ───────────────────────────────────────────────────────────

    public function test_update_modifies_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name']);

        $response = $this->putJson(self::BASE."/{$product->id}", [
            'name' => 'New Name',
            'sku' => $product->sku,
            'price' => 199.00,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'New Name']);
    }

    public function test_update_allows_same_sku_for_current_product(): void
    {
        $product = Product::factory()->create(['sku' => 'KEEP-SKU']);

        $response = $this->putJson(self::BASE."/{$product->id}", [
            'name' => $product->name,
            'sku' => 'KEEP-SKU',
            'price' => $product->price,
        ]);

        $response->assertStatus(200)
            ->assertJsonMissingValidationErrors('sku');
    }

    public function test_update_validates_required_fields(): void
    {
        $product = Product::factory()->create();

        $response = $this->putJson(self::BASE."/{$product->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'sku', 'price']);
    }

    // ── Destroy ──────────────────────────────────────────────────────────

    public function test_destroy_deletes_product_and_returns_204(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson(self::BASE."/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
