<?php

namespace App\Http\Controllers;

use App\Enums\Items\ItemSlot;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Response;

class GearPlannerController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return inertia('GearPlanner/Index', [
            'helms' => Item::query()->where('slot', ItemSlot::Head)->get(),
            'necks' => Item::query()->where('slot', ItemSlot::Neck)->get(),
        ]);
    }
}
