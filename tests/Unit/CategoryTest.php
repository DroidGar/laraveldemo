<?php
namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_category()
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas('categories', [
            'category' => $category->category
        ]);
    }

    /** @test */
    public function it_has_a_valid_category_name()
    {
        $category = Category::factory()->make([
            'category' => 'Valid Category'
        ]);

        $this->assertNotEmpty($category->category);
        $this->assertIsString($category->category);
    }
}
