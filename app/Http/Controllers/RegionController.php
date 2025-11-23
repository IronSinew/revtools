<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Mob;
use App\Models\Region;
use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Response;

class RegionController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Map/Index', [
            'regions' => fn () => Region::query()->whereNotNull('coordinates')->get(),
        ]);
    }

    public function show(Request $request, Region $region): Response
    {
        $region->load(['rooms.mobs', 'rooms.items', 'rooms.exitRegion']);
        $region->rooms->each(function (Room $room) {
            $room->items->sortBy([['type', 'asc'], 'effective_required_level', 'desc']);

            return $room;
        });

        return inertia('Map/Show', [
            'region' => $region,
            'search' => $request->input('search'),
            'mobs' => fn () => Mob::whereHas('rooms', fn ($query) => $query
                ->where('region_id', $region->id))
                ->orderBy('level', 'desc')
                ->get(),
            'items' => fn () => Item::whereHas('mobs.rooms', fn ($query) => $query
                ->where('region_id', $region->id))
                ->orderBy('type', 'desc')
                ->orderBy('effective_required_level', 'desc')
                ->get(),
        ]);
    }
}
