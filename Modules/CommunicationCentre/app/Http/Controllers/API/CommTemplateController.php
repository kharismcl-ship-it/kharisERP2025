<?php

namespace Modules\CommunicationCentre\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\CommunicationCentre\Http\Resources\CommTemplateResource;
use Modules\CommunicationCentre\Models\CommTemplate;

class CommTemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CommTemplate::query();

        // Filter by company if provided
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by channel if provided
        if ($request->has('channel')) {
            $query->where('channel', $request->channel);
        }

        // Filter by active status if provided
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name or code
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('code', 'like', '%'.$request->search.'%');
            });
        }

        $templates = $query->orderBy('name')->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => CommTemplateResource::collection($templates),
            'meta' => [
                'current_page' => $templates->currentPage(),
                'last_page' => $templates->lastPage(),
                'per_page' => $templates->perPage(),
                'total' => $templates->total(),
            ],
        ]);
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'code' => 'required|string|max:100|unique:comm_templates,code',
            'channel' => 'required|string|in:email,sms,whatsapp,database',
            'provider' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'subject' => 'required|string',
            'body' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'provider_config_id' => 'nullable|exists:comm_provider_configs,id',
        ]);

        $template = CommTemplate::create($validated);

        return response()->json([
            'message' => 'Template created successfully',
            'data' => new CommTemplateResource($template),
        ], 201);
    }

    /**
     * Display the specified template.
     */
    public function show(CommTemplate $template): JsonResponse
    {
        return response()->json([
            'data' => new CommTemplateResource($template),
        ]);
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, CommTemplate $template): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'code' => 'required|string|max:100|unique:comm_templates,code,'.$template->id,
            'channel' => 'required|string|in:email,sms,whatsapp,database',
            'provider' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'subject' => 'required|string',
            'body' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'provider_config_id' => 'nullable|exists:comm_provider_configs,id',
        ]);

        $template->update($validated);

        return response()->json([
            'message' => 'Template updated successfully',
            'data' => new CommTemplateResource($template),
        ]);
    }

    /**
     * Remove the specified template.
     */
    public function destroy(CommTemplate $template): JsonResponse
    {
        // Check if template is being used
        if ($template->messages()->exists()) {
            return response()->json([
                'message' => 'Cannot delete template that is being used by messages',
            ], 422);
        }

        $template->delete();

        return response()->json([
            'message' => 'Template deleted successfully',
        ]);
    }

    /**
     * Preview template with sample data.
     */
    public function preview(Request $request, CommTemplate $template): JsonResponse
    {
        $sampleData = $request->validate([
            'data' => 'nullable|array',
        ])['data'] ?? [];

        // Default sample data
        $defaultData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'company' => 'Example Company',
            'date' => now()->format('Y-m-d'),
            'amount' => '100.00',
            'reference' => 'REF-123456',
        ];

        $data = array_merge($defaultData, $sampleData);

        $renderedSubject = $this->renderTemplate($template->subject, $data);
        $renderedBody = $this->renderTemplate($template->body, $data);

        return response()->json([
            'original_subject' => $template->subject,
            'original_body' => $template->body,
            'rendered_subject' => $renderedSubject,
            'rendered_body' => $renderedBody,
            'sample_data' => $data,
        ]);
    }

    /**
     * Render template with data.
     */
    private function renderTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace(
                ['{{'.$key.'}}', '{{ '.$key.' }}'],
                $value,
                $template
            );
        }

        return $template;
    }
}
