<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportQuestDataController
{
    public function __invoke(Request $request): JsonResponse
    {
        $valid = $request->validate([
            'names' => ['required', 'array'],
        ]);

        return response()->json(
            Quest::whereIn('name', $valid['names'])->pluck('id')
        );
    }
}
