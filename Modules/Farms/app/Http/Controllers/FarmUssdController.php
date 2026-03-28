<?php

namespace Modules\Farms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Farms\Services\FarmUssdService;

class FarmUssdController extends Controller
{
    public function __construct(private FarmUssdService $service) {}

    /**
     * Africa's Talking USSD webhook endpoint.
     * POST /farm-ussd
     */
    public function handle(Request $request): Response
    {
        $sessionId   = $request->input('sessionId', '');
        $phoneNumber = $request->input('phoneNumber', '');
        $text        = $request->input('text', '');

        $responseText = $this->service->handle($sessionId, $phoneNumber, $text);

        return response($responseText, 200, ['Content-Type' => 'text/plain']);
    }
}