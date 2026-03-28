<?php

namespace Modules\Requisition\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class PwaController extends Controller
{
    public function manifest(): JsonResponse
    {
        return response()->json([
            'name'             => 'KharisERP Requisitions',
            'short_name'       => 'Requisitions',
            'start_url'        => '/staff',
            'display'          => 'standalone',
            'background_color' => '#ffffff',
            'theme_color'      => '#0d9488',
            'icons'            => [
                [
                    'src'   => '/images/icon-192.png',
                    'sizes' => '192x192',
                    'type'  => 'image/png',
                ],
                [
                    'src'   => '/images/icon-512.png',
                    'sizes' => '512x512',
                    'type'  => 'image/png',
                ],
            ],
        ]);
    }

    public function serviceWorker(): Response
    {
        $content = "// KharisERP Requisitions Service Worker\nself.addEventListener('fetch', function(event) {});";

        return response($content, 200, ['Content-Type' => 'application/javascript']);
    }
}