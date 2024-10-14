<?php

namespace App\Http\Controllers;

use App\Services\EntityService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;

class EntityController extends Controller
{
    protected EntityService $entityService;

    public function __construct(EntityService $publicApiService)
    {
        $this->entityService = $publicApiService;
    }

    /**
     * @throws GuzzleException
     */
    public function fetchAndStoreEntities(): JsonResponse
    {
        $message = $this->entityService->getEntries();
        return response()->json(['message' => $message]);
    }

    public function getEntitiesByCategoryName(string $categoryName): JsonResponse
    {
        $entities = $this->entityService->getEntitiesByCategoryName($categoryName);
        return response()->json(['success' => true, 'data' => $entities]);
    }
}
