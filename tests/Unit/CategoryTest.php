<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_creation()
    {
        $category = Category::create([
            'name' => 'Test Category',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
        ]);
    }

    public function test_category_validation()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Creating a category without a name should fail
        Category::create([]);
    }

    public function test_category_product_relationship()
    {
        $category = Category::create(['name' => 'Test Category']);

        $product = Product::create([
            'name' => 'Test Product',
            'description' => 'This is a test product.',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $category->products()->attach($product->id);

        $this->assertTrue($category->products->contains($product));
    }
}
