<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestController extends Controller
{
    public function index(Request $request): Response
    {
        return inertia('Quest/Index', [
            'table' => Inertia::defer(fn () => Quest::query()
                ->paginate(
                    perPage: $request->get('rows') ?? \Config::get('database.paginate.per_page'),
                    page: $request->get('page') + 1
                ))
        ]);
    }
}
