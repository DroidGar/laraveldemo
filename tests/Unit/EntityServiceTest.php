<?php

namespace Tests\Unit;

use App\Services\EntityService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use App\Models\Category;

class EntityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws GuzzleException
     */
    public function testGetEntries()
    {

        $mockClient = Mockery::mock(Client::class);
        $mockResponse = new Response(200, [], json_encode([
            'entries' => [
                [
                    'API' => 'Google Books',
                    'Description' => 'Search and view books',
                    'Category' => 'Books',
                    'Link' => 'https://books.google.com',
                ],
                [
                    'API' => 'Google Books',
                    'Description' => 'Search and view books',
                    'Category' => 'Service',
                    'Link' => 'https://books.google.com',
                ],

            ],
        ]));

        $mockClient->shouldReceive('get')->andReturn($mockResponse);

        $category = Category::factory()->create(['category' => 'Books']);
        $this->assertDatabaseCount('categories', 1);

        $entityService = new EntityService($mockClient);

        $entityService->getEntries();

        $this->assertDatabaseCount('entities', 1);
        $this->assertDatabaseHas('entities', [
            'api' => 'Google Books',
            'description' => 'Search and view books',
            'link' => 'https://books.google.com',
            'category_id' => $category->id,
        ]);
    }
}
