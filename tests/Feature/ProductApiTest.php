<?php

namespace Tests\Feature;

use App\Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Cannot create a product without required fields
     * This ensures validation is working correctly and returns proper 422 errors
     */
    public function test_cannot_create_product_without_required_fields()
    {
        $response = $this->postJson('/api/product', []);

        $response->assertStatus(422);
    }

    /**
     * Test: Can create product successfully
     * This validates that the API saves a product and returns correct response
     */
    public function test_can_create_product_successfully()
    {
        $payload = [
            'name' => 'Gaming Laptop',
            'description' => 'High performance laptop',
            'price' => 1500,
        ];

        $response = $this->postJson('/api/product', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Product created successfully.',
                'data' => ['name' => 'Gaming Laptop', 'price' => 1500],
            ]);

        $this->assertDatabaseHas('products', ['name' => 'Gaming Laptop']);
    }

    /**
     * Test: Can create product without a description
     * Description is optional; the response should expose it as null under the "description" key
     */
    public function test_can_create_product_without_description()
    {
        $response = $this->postJson('/api/product', [
            'name' => 'Mouse',
            'price' => 25,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Mouse')
            ->assertJsonPath('data.description', null)
            ->assertJsonStructure(['data' => ['id', 'name', 'price', 'description']]);

        $this->assertDatabaseHas('products', ['name' => 'Mouse', 'description' => null]);
    }

    /**
     * Test: Cannot update product without required fields
     * Checks validation for update endpoint
     */
    public function test_cannot_update_product_without_required_fields()
    {
        $response = $this->putJson('/api/product', []);

        $response->assertStatus(422);
    }

    /**
     * Test: Can update product successfully
     * Verifies product update works and DB reflects changes
     */
    public function test_can_update_product_successfully()
    {
        // Create a product in DB for updating
        $product = Product::factory()->create([
            'name' => 'Old Laptop',
            'description' => 'Old model',
            'price' => 1000,
        ]);

        $payload = [
            'id' => $product->id,
            'name' => 'Updated Laptop',
            'description' => 'New model',
            'price' => 1200,
        ];

        $response = $this->putJson('/api/product', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product updated successfully.',
                'data' => ['id' => $product->id, 'name' => 'Updated Laptop', 'price' => 1200],
            ]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Laptop']);
    }

    /**
     * Test: Cannot delete product without ID
     * Validates delete endpoint requires product ID
     */
    public function test_cannot_delete_product_without_id()
    {
        $response = $this->deleteJson('/api/product', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['id'],
            ]);
    }

    /**
     * Test: Can delete product successfully
     * Ensures product deletion works and product is removed from DB
     */
    public function test_can_delete_product_successfully()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson('/api/product', ['id' => $product->id]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Product deleted successfully.',
            ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
