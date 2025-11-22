<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Response;

class MultiItemController extends Controller
{
    public function __invoke(Request $request, ?string $items = null): Response
    {
        // We only allow a max of 3 items to be compared
        $items = array_slice(explode('...', $items), 0, 3);
        $itemList = Item::whereIn('slug', $items)->get();

        return inertia('Item/MultiItemView', [
            'items' => fn () => $itemList,
        ]);
    }
}
