<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Quest/Index', [
            'table' => Inertia::defer(fn () => Quest::query()
                ->with(['items', 'mob'])
                ->when($request->input('filters.reward_type.value'), fn ($query, $filters) => $query
                    ->whereJsonContains('reward_types', $request->input('filters.reward_type.value'))
                )
                ->when($request->input('filters.class.value'), fn ($query, $filters) => $query
                    ->whereJsonContains('quests.required_class', $request->input('filters.class.value'))
                )
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
                ->when(! $request->input('sort'), fn ($query, $filters) => $query->orderBy('id', 'ASC'))
                ->paginate(
                    perPage: $request->get('rows') ?? \Config::get('database.paginate.per_page'),
                    page: $request->get('page') + 1
                ))
        ]);
    }

    public function show(Request $request, Quest $quest): Response
    {
        return inertia('Quest/Show', [
            'quest' => fn () => $quest->load(['items', 'mob']),
            'quest_chain' =>  fn () => Quest::with('mob')->orderBy('id')->findMany($quest->quest_chain),
        ]);
    }
}
