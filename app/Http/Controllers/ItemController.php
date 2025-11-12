<?php

namespace App\Http\Controllers;

use App\Enums\ClassType;
use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Item/ItemIndex', [
            'table' => Inertia::defer(fn () => Item::query()
                ->when($request->input('filters.organization_id.value'), fn ($query, $filters) => $query
                    ->whereIn('organization_id', $request->input('filters.organization_id.value'))
                )
                ->when($request->input('filters.type.value'), fn ($query, $filters) => $query
                    ->whereIn('type', $request->input('filters.type.value'))
                )
                ->when($request->input('filters.sub_type.value'), fn ($query, $filters) => $query
                    ->whereIn('sub_type', $request->input('filters.sub_type.value'))
                )
                ->when($request->input('filters.slot.value'), fn ($query, $filters) => $query
                    ->whereIn('slot', $request->input('filters.slot.value'))
                )
                ->when($request->input('filters.class.value'), function ($query, $filters) use ($request) {
                    $class = ClassType::tryFrom($request->input('filters.class.value'));
                    $query
                        ->whereIn('sub_type', $class->usableSubTypes())
                        ->where(function($query) use ($class, $request) {
                            $query->whereJsonContains('requirements->classes', $class)
                                ->orWhereNull('requirements->classes');
                        });
                })
                ->when($request->input('filters.effective_required_level.value'), fn ($query, $filters) => $query
                    ->whereBetween('effective_required_level', $request->input('filters.effective_required_level.value'))
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
                ->when(! $request->input('sort'), fn ($query, $filters) => $query->orderBy('id', 'DESC'))
                ->paginate(
                    perPage: $request->get('rows') ?? \Config::get('database.paginate.per_page'),
                    page: $request->get('page') + 1
                )),
        ]);
    }

    public function show(Item $item, Request $request): Response
    {
        return inertia('Item/ItemView', [
            'item' => fn () => $item,
        ]);
    }
}
