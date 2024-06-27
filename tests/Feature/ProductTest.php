<?php 
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_creation()
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Test Product')
                 ->assertJsonPath('data.price', 99.99)
                 ->assertJsonPath('data.stock', 10);
    }

    public function test_product_validation()
    {
        // Missing required fields
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'price', 'stock']);
    }

    public function test_product_update()
    {
        $product = Product::create([
            'name' => 'Original Product',
            'price' => 59.99,
            'stock' => 15,
        ]);

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => 'Updated Product',
            'price' => 79.99,
            'stock' => 20,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Updated Product')
                 ->assertJsonPath('data.price', 79.99)
                 ->assertJsonPath('data.stock', 20);
    }

    public function test_product_deletion()
    {
        $product = Product::create([
            'name' => 'Product to Delete',
            'price' => 29.99,
            'stock' => 5,
        ]);

        $response = $this->deleteJson('/api/products/' . $product->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
