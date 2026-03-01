<?php

namespace Modules\CommunicationCentre\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\CommunicationCentre\Models\CommTemplate;

class CommunicationCentreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('communicationcentre::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('communicationcentre::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('communicationcentre::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('communicationcentre::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}

    /**
     * Get available communication templates for discovery.
     */
    public function templates(Request $request): JsonResponse
    {
        try {
            $companyId = $request->user()->current_company_id ?? null;

            $templates = CommTemplate::query()
                ->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->orWhereNull('company_id');
                })
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'description', 'channel', 'company_id', 'is_active']);

            return response()->json([
                'success' => true,
                'data' => $templates,
                'meta' => [
                    'total' => $templates->count(),
                    'company_id' => $companyId,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch templates: '.$e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Get template details by code.
     */
    public function templateByCode(Request $request, string $code): JsonResponse
    {
        try {
            $companyId = $request->user()->current_company_id ?? null;

            $template = CommTemplate::where('code', $code)
                ->where(function ($query) use ($companyId) {
                    $query->where('company_id', $companyId)
                        ->orWhereNull('company_id');
                })
                ->where('is_active', true)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $template->only([
                    'id', 'code', 'name', 'description', 'channel',
                    'subject', 'body', 'company_id', 'is_active',
                ]),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found: '.$e->getMessage(),
            ], 404);
        }
    }
}
