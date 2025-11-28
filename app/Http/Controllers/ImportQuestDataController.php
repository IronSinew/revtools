<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImportQuestDataController
{
    public function __invoke(Request $request): JsonResponse
    {
        $valid = $request->validate([
            'names' => ['required', 'array'],
        ]);

        $questIdsCompleted = [];
        foreach ($valid['names'] as $name) {
            $quest = Quest::where('name', Str::trim($name))->first();

            if ($quest) {
                $questIdsCompleted[] = $quest->id;
            }
        }

        return response()->json([
            'questsCompleted' => $questIdsCompleted,
        ]);
    }
}