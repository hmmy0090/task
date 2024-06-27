<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_creation()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);
    }

    public function test_product_validation()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Creating a product without required fields should fail
        Product::create([]);
    }

    public function test_product_category_relationship()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $category = Category::create(['name' => 'Test Category']);
        $product->categories()->attach($category->id);

        $this->assertTrue($product->categories->contains($category));
    }
}
