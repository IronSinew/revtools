<?php

namespace App\Http\Controllers;

use App\Enums\SearchableType;
use App\Models\Item;
use App\Models\Mob;
use App\Models\Quest;
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
            ->groupBy('type');

        return response()->json([
            'typesense' => $rawTypesesense,
            'data' => [
                'items' => Item::findMany(
                    $groupedIds->get(SearchableType::Item->value)?->pluck('id')
                )
                    ->keyBy('id'),
                'mobs' => Mob::findMany(
                    $groupedIds->get(SearchableType::Mob->value)?->pluck('id')
                )
                    ->keyBy('id'),
                'quests' => Quest::findMany(
                    $groupedIds->get(SearchableType::Quest->value)?->pluck('id')

                )
                    ->keyBy('id')
            ],
        ]);
    }
}
