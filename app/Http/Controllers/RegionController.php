<?php

namespace App\Http\Controllers;

use App\Models\Region;
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
        return inertia('Map/Show', [
            'region' => $region->load(['rooms.mobs', 'rooms.items', 'rooms.exitRegion']),
            'search' => $request->input('search'),
        ]);
    }
}
