<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Entity;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EntityService
{
    public $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get entries by specified categories.
     *
     * @throws GuzzleException
     */
    public function getEntries(): string
    {
        $categories = Category::all()->toArray();
        $categoryNames = array_column($categories, 'category');

        $response = $this->client->get('https://raw.githubusercontent.com/marcelscruz/public-apis/refs/heads/main/db/resources.json');
        $entries = json_decode($response->getBody()->getContents(), true)['entries'];

        $filteredEntries = array_filter($entries, fn($entry) => in_array($entry['Category'], $categoryNames));
        $arrayValues = array_values($filteredEntries);

        $totalResults = count($entries);
        $categoryCounts = array_fill_keys($categoryNames, 0);

        foreach ($arrayValues as $key => $value) {
            $category = $value['Category'];
            if (isset($categoryCounts[$category])) {
                $categoryCounts[$category]++;
            }

            $arrayValues[$key] = [
                'api' => $value['API'],
                'description' => $value['Description'],
                'category' => $category,
                'link' => $value['Link'],
                'category_id' => $categories[array_search($category, $categoryNames)]['id'],
            ];

            Entity::updateOrCreate(['api' => $value['API']], $arrayValues[$key]);
        }

        $categoryCountsString = implode(', ', array_map(
            fn($category, $count) => "$category found: $count",
            array_keys($categoryCounts),
            $categoryCounts
        ));

        return "Total results: $totalResults, $categoryCountsString";
    }

    public function getEntitiesByCategoryName(string $categoryName)
    {
        $category = Category::where('category', $categoryName)->first();
        $entities = Entity::where('category_id', $category->id)->select('api', 'description', 'link', 'category_id')
            ->with('category')
            ->get();
        return $entities->makeHidden('category_id');
    }
}
