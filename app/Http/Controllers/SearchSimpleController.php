<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSimpleController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $rawTypesesense = collect(Item::search($request->search)->raw()['hits'])
            ->pluck('document')
            ->toArray();

        $groupedIds = collect($rawTypesesense)
            ->pluck('id')
            ->groupBy('type');

        return response()->json([
            'typesense' => $rawTypesesense,
            'data' => [
                'items' => Item::findMany($groupedIds)->keyBy('id'),
            ],
        ]);
    }
}
