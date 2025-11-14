<?php

namespace App\Http\Controllers;

use App\Enums\SearchableType;
use App\Models\Item;
use App\Models\Mob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Base64DecodeImageController extends Controller {
    public function __invoke(Request $request): Response {
        list(, $base64_data) = explode(';', $request->get('q'));
        list(, $base64_data) = explode(',', $base64_data);

        return response(base64_decode($base64_data))
            ->header('Content-Type', 'image/png');
    }
}
