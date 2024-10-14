<?php
namespace Tests\Unit;

use App\Models\Category;
use App\Models\Entity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_entity()
    {
        $category = Category::factory()->create();
        $entity = Entity::factory()->create([
            'api' => 'Test API',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'link' => 'http://example.com'
        ]);

        $this->assertDatabaseHas('entities', [
            'api' => 'Test API',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'link' => 'http://example.com'
        ]);
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $category = Category::factory()->create();
        $entity = Entity::factory()->create([
            'category_id' => $category->id
        ]);

        $this->assertInstanceOf(Category::class, $entity->category);
        $this->assertEquals($category->id, $entity->category->id);
    }
}
