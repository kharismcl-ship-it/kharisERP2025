<?php

namespace Modules\Requisition\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\HR\Models\Employee;
use Modules\Requisition\Http\Resources\RequisitionApiResource;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionItem;

class RequisitionApiController extends Controller
{
    /**
     * Resolve the authenticated user's company_id via their employee record.
     */
    private function resolveCompanyId(): ?int
    {
        $user = auth()->user();
        if (! $user) return null;

        $employee = Employee::where('user_id', $user->id)->first();
        return $employee?->company_id;
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = $this->resolveCompanyId();
        if (! $companyId) {
            return response()->json(['message' => 'No employee record found for this user.'], 403);
        }

        $query = Requisition::where('company_id', $companyId);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('request_type', $request->input('type'));
        }

        $requisitions = $query
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'data'    => RequisitionApiResource::collection($requisitions->items()),
            'meta'    => [
                'current_page'  => $requisitions->currentPage(),
                'per_page'      => $requisitions->perPage(),
                'total'         => $requisitions->total(),
                'last_page'     => $requisitions->lastPage(),
            ],
            'message' => 'OK',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $companyId = $this->resolveCompanyId();
        if (! $companyId) {
            return response()->json(['message' => 'No employee record found for this user.'], 403);
        }

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'request_type'   => 'required|string|in:' . implode(',', array_keys(Requisition::TYPES)),
            'description'    => 'nullable|string',
            'urgency'        => 'nullable|string|in:' . implode(',', array_keys(Requisition::URGENCIES)),
            'cost_centre_id' => 'nullable|integer|exists:cost_centres,id',
        ]);

        $employee = Employee::where('user_id', auth()->id())->where('company_id', $companyId)->first();

        $requisition = Requisition::create([
            'company_id'            => $companyId,
            'requester_employee_id' => $employee?->id,
            'title'                 => $validated['title'],
            'request_type'          => $validated['request_type'],
            'urgency'               => $validated['urgency'] ?? 'medium',
            'description'           => $validated['description'] ?? null,
            'cost_centre_id'        => $validated['cost_centre_id'] ?? null,
            'status'                => 'draft',
        ]);

        return response()->json([
            'data'    => new RequisitionApiResource($requisition),
            'message' => 'Requisition created successfully.',
        ], 201);
    }

    public function show(Requisition $requisition): JsonResponse
    {
        $companyId = $this->resolveCompanyId();

        if ($requisition->company_id !== $companyId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $requisition->load(['items', 'approvers']);

        return response()->json([
            'data'    => new RequisitionApiResource($requisition),
            'message' => 'OK',
        ]);
    }

    public function updateStatus(Request $request, Requisition $requisition): JsonResponse
    {
        $companyId = $this->resolveCompanyId();

        if ($requisition->company_id !== $companyId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_keys(Requisition::STATUSES)),
        ]);

        $requisition->update(['status' => $validated['status']]);

        return response()->json([
            'data'    => new RequisitionApiResource($requisition->fresh()),
            'message' => 'Status updated.',
        ]);
    }

    public function submit(Requisition $requisition): JsonResponse
    {
        $companyId = $this->resolveCompanyId();

        if ($requisition->company_id !== $companyId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($requisition->status !== 'draft') {
            return response()->json(['message' => 'Only draft requisitions can be submitted.'], 422);
        }

        $requisition->update(['status' => 'submitted']);

        return response()->json([
            'data'    => new RequisitionApiResource($requisition->fresh()),
            'message' => 'Requisition submitted.',
        ]);
    }

    public function items(Requisition $requisition): JsonResponse
    {
        $companyId = $this->resolveCompanyId();

        if ($requisition->company_id !== $companyId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'data'    => $requisition->items->toArray(),
            'message' => 'OK',
        ]);
    }

    public function addItem(Request $request, Requisition $requisition): JsonResponse
    {
        $companyId = $this->resolveCompanyId();

        if ($requisition->company_id !== $companyId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($requisition->status !== 'draft') {
            return response()->json(['message' => 'Items can only be added to draft requisitions.'], 422);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'quantity'    => 'required|numeric|min:0.001',
            'unit'        => 'nullable|string|max:50',
            'unit_cost'   => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);

        $item = RequisitionItem::create([
            'requisition_id' => $requisition->id,
            'description'    => $validated['description'],
            'quantity'       => $validated['quantity'],
            'unit'           => $validated['unit'] ?? 'pcs',
            'unit_cost'      => $validated['unit_cost'] ?? null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'data'    => $item->toArray(),
            'message' => 'Item added.',
        ], 201);
    }
}