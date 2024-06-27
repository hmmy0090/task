<?php 
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_assign_categories_to_product()
    {
        $product = Product::create([
            'name' => 'Product with Categories',
            'price' => 49.99,
            'stock' => 10,
        ]);

        $categories = [
            Category::create(['name' => 'Category 1']),
            Category::create(['name' => 'Category 2']),
            Category::create(['name' => 'Category 3']),
        ];

        $response = $this->postJson("/api/products/{$product->id}/categories", [
            'categories' => array_map(function ($category) {
                return $category->id;
            }, $categories),
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.categories.0.id', $categories[0]->id);

        $product->refresh();
        $this->assertCount(3, $product->categories);
    }

    public function test_filter_products_by_category()
    {
        $category = Category::create(['name' => 'Filtered Category']);
        $product = Product::create([
            'name' => 'Filtered Product',
            'price' => 29.99,
            'stock' => 20,
        ]);

        $product->categories()->attach($category->id);

        $response = $this->getJson("/api/categories/{$category->id}/products");

        $response->assertStatus(200)
                 ->assertJsonPath('data.0.id', $product->id);
    }
}
