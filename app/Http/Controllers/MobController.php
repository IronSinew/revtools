<?php

namespace App\Http\Controllers;

use App\Enums\ClassType;
use App\Models\Mob;
use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MobController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Mob/MobIndex', [
            'table' => Inertia::defer(fn () => Mob::query()
                ->when($request->input('filters.type.value'), fn ($query, $filters) => $query
                    ->whereIn('type', $request->input('filters.type.value'))
                )
                ->when($request->input('filters.tier.value'), fn ($query, $filters) => $query
                    ->whereIn('tier', $request->input('filters.tier.value'))
                )
//                ->when($request->input('filters.class.value'), function ($query, $filters) use ($request) {
//                    $class = ClassType::tryFrom($request->input('filters.class.value'));
//                    $query
//                        ->whereIn('sub_type', $class->usableSubTypes())
//                        ->where(function($query) use ($class, $request) {
//                            $query->whereJsonContains('requirements->classes', $class)
//                                ->orWhereNull('requirements->classes');
//                        });
//                })
                ->when($request->input('filters.level.value'), fn ($query, $filters) => $query
                    ->whereBetween('level', $request->input('filters.level.value'))
                )
                ->when($request->input('sort'), function (Builder $query) use ($request) {
                    $sortKeys = collect($request->input('sort'))->pluck('field')->filter()->values()->toArray();
                    $sortDir = collect($request->input('sort'))->pluck('order')->filter()->values()
                        ->map(fn (string $val) => $val === '1' ? 'ASC' : 'DESC')
                        ->toArray();
                    $sortedValues = array_combine($sortKeys, $sortDir);
                    foreach ($sortedValues as $key => $direction) {
                        $query->orderBy($key, $direction);
                    }
                })
                ->with('items')
                ->when(! $request->input('sort'), fn ($query, $filters) => $query->orderBy('id', 'DESC'))
                ->paginate(
                    perPage: $request->get('rows') ?? \Config::get('database.paginate.per_page'),
                    page: $request->get('page') + 1
                )),
        ]);
    }

    public function show(Mob $Mob, Request $request): Response
    {
        $Mob->loadMissing(['items']);

        return inertia('Mob/MobView', [
            'mob' => fn () => $Mob,
            'regions' => fn () => Region::query()
                ->whereHas('rooms.mobs', fn ($query) => $query->where('mob_id', $Mob->id))
                ->get(),
        ]);
    }
}
