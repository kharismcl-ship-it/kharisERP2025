<?php

namespace Modules\Requisition\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequisitionApiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'reference'             => $this->reference,
            'title'                 => $this->title,
            'description'           => $this->description,
            'request_type'          => $this->request_type,
            'request_type_label'    => \Modules\Requisition\Models\Requisition::TYPES[$this->request_type] ?? $this->request_type,
            'urgency'               => $this->urgency,
            'status'                => $this->status,
            'status_label'          => \Modules\Requisition\Models\Requisition::STATUSES[$this->status] ?? $this->status,
            'total_estimated_cost'  => $this->total_estimated_cost,
            'cost_centre_id'        => $this->cost_centre_id,
            'due_by'                => $this->due_by?->toDateString(),
            'approved_at'           => $this->approved_at?->toDateTimeString(),
            'fulfilled_at'          => $this->fulfilled_at?->toDateTimeString(),
            'company_id'            => $this->company_id,
            'requester_employee_id' => $this->requester_employee_id,
            'created_at'            => $this->created_at?->toDateTimeString(),
            'updated_at'            => $this->updated_at?->toDateTimeString(),
            'items'                 => $this->whenLoaded('items', fn () =>
                $this->items->map(fn ($item) => [
                    'id'                => $item->id,
                    'description'       => $item->description,
                    'quantity'          => $item->quantity,
                    'unit'              => $item->unit,
                    'unit_cost'         => $item->unit_cost,
                    'total_cost'        => $item->total_cost,
                    'fulfilled_quantity' => $item->fulfilled_quantity,
                ])
            ),
            'approvers'             => $this->whenLoaded('approvers', fn () =>
                $this->approvers->map(fn ($approver) => [
                    'id'         => $approver->id,
                    'role'       => $approver->role,
                    'decision'   => $approver->decision,
                    'decided_at' => $approver->decided_at?->toDateTimeString(),
                ])
            ),
        ];
    }
}