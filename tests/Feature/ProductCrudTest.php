<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    // ── Index ────────────────────────────────────────────────────────────

    public function test_index_lists_products(): void
    {
        $products = Product::factory(3)->create();

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

    // ── Create ───────────────────────────────────────────────────────────

    public function test_create_form_loads(): void
    {
        $response = $this->get(route('products.create'));

        $response->assertStatus(200);
        $response->assertSee('New Product');
    }

    // ── Store ────────────────────────────────────────────────────────────

    public function test_store_creates_product(): void
    {
        $data = [
            'name' => 'Red Hat Enterprise Linux',
            'sku' => 'RHEL-001',
            'price' => '299.99',
            'description' => 'Enterprise Linux subscription.',
        ];

        $response = $this->post(route('products.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['sku' => 'RHEL-001', 'name' => 'Red Hat Enterprise Linux']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post(route('products.store'), []);

        $response->assertSessionHasErrors(['name', 'sku', 'price']);
    }

    public function test_store_validates_duplicate_sku(): void
    {
        $existing = Product::factory()->create(['sku' => 'UNIQUE-001']);

        $response = $this->post(route('products.store'), [
            'name' => 'Another Product',
            'sku' => 'UNIQUE-001',
            'price' => '49.99',
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_store_validates_negative_price(): void
    {
        $response = $this->post(route('products.store'), [
            'name' => 'Bad Price Product',
            'sku' => 'BAD-PRICE',
            'price' => '-10',
        ]);

        $response->assertSessionHasErrors('price');
    }

    // ── Show ─────────────────────────────────────────────────────────────

    public function test_show_displays_product(): void
    {
        $product = Product::factory()->create(['name' => 'OpenShift Container Platform']);

        $response = $this->get(route('products.show', $product));

        $response->assertStatus(200);
        $response->assertSee('OpenShift Container Platform');
        $response->assertSee($product->sku);
    }

    // ── Edit ─────────────────────────────────────────────────────────────

    public function test_edit_form_loads_with_product_data(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('products.edit', $product));

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee($product->sku);
    }

    // ── Update ───────────────────────────────────────────────────────────

    public function test_update_modifies_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old Name', 'sku' => 'OLD-SKU']);

        $response = $this->put(route('products.update', $product), [
            'name' => 'New Name',
            'sku' => 'OLD-SKU',
            'price' => '199.00',
            'description' => 'Updated description.',
        ]);

        $response->assertRedirect(route('products.show', $product));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'New Name']);
    }

    public function test_update_allows_same_sku_for_current_product(): void
    {
        $product = Product::factory()->create(['sku' => 'SAME-SKU']);

        $response = $this->put(route('products.update', $product), [
            'name' => $product->name,
            'sku' => 'SAME-SKU',
            'price' => $product->price,
        ]);

        $response->assertSessionDoesntHaveErrors('sku');
    }

    public function test_update_validates_required_fields(): void
    {
        $product = Product::factory()->create();

        $response = $this->put(route('products.update', $product), []);

        $response->assertSessionHasErrors(['name', 'sku', 'price']);
    }

    // ── Destroy ──────────────────────────────────────────────────────────

    public function test_destroy_deletes_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
