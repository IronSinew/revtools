<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Base64DecodeImageController extends Controller
{
    public function __invoke(Request $request): Response
    {
        [, $base64_data] = explode(';', $request->get('q'));
        [, $base64_data] = explode(',', $base64_data);

        return response(base64_decode($base64_data))
            ->header('Content-Type', 'image/png');
    }
}
