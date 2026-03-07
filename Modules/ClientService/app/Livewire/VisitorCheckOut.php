<?php

namespace Modules\ClientService\Livewire;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Modules\ClientService\Models\CsVisitor;
use Modules\ClientService\Models\CsVisitorBadge;

class VisitorCheckOut extends Component
{
    #[Locked]
    public int $companyId;

    public string $companyName = '';

    /**
     * Screens:
     *  confirm      — show visitor details, prompt for badge return
     *  badge_return — collect & confirm the physical badge code
     *  success      — checked out
     *  already_out  — already checked out previously
     *  not_found    — token not recognised
     */
    public string $screen = 'confirm';

    #[Locked]
    public ?int    $visitorId   = null;
    public ?string $visitorName = null;
    public ?string $badgeCode   = null;
    public ?string $checkedInAt = null;

    /** What the visitor types in the badge-return confirmation screen */
    public string $enteredBadge = '';
    public ?string $badgeError  = null;

    public function mount(Company $company, string $checkInToken): void
    {
        $this->companyId   = $company->id;
        $this->companyName = $company->name;

        $visitor = CsVisitor::where('check_in_token', $checkInToken)
            ->where('company_id', $company->id)
            ->first();

        if (! $visitor) {
            $this->screen = 'not_found';
            return;
        }

        if ($visitor->check_out_at !== null) {
            $this->screen      = 'already_out';
            $this->visitorName = $visitor->full_name;
            $this->checkedInAt = $visitor->check_in_at->format('g:i A');
            return;
        }

        $this->visitorId   = $visitor->id;
        $this->visitorName = $visitor->full_name;
        $this->badgeCode   = $visitor->badge_number;
        $this->checkedInAt = $visitor->check_in_at->format('g:i A \o\n D j M');
    }

    /** Move from confirm screen → badge return screen. */
    public function proceedToBadgeReturn(): void
    {
        // If no badge was issued, skip badge confirmation and go straight to checkout
        if (blank($this->badgeCode)) {
            $this->doCheckOut();
            return;
        }

        $this->enteredBadge = '';
        $this->badgeError   = null;
        $this->screen       = 'badge_return';
    }

    /** Validate the badge code entered and complete checkout. */
    public function confirmBadgeReturn(): void
    {
        $entered = strtoupper(trim($this->enteredBadge));

        if ($entered !== strtoupper($this->badgeCode)) {
            $this->badgeError = 'Badge code does not match. Please check and try again.';
            return;
        }

        $this->badgeError = null;
        $this->doCheckOut();
    }

    /** Perform the actual checkout (shared by both paths). */
    private function doCheckOut(): void
    {
        $visitor = CsVisitor::find($this->visitorId);

        if (! $visitor || $visitor->check_out_at !== null) {
            $this->screen = 'already_out';
            return;
        }

        // Check out the main visitor
        $visitor->update(['check_out_at' => now()]);

        // Release the badge back to available
        CsVisitorBadge::where('issued_to_visitor_id', $visitor->id)
            ->where('status', 'issued')
            ->first()
            ?->revokeFromVisitor('Visitor checked out via kiosk QR scan.');

        // Also check out group members who are still inside
        $visitor->groupMembers()
            ->whereNull('check_out_at')
            ->each(function (CsVisitor $member): void {
                $member->update(['check_out_at' => now()]);

                CsVisitorBadge::where('issued_to_visitor_id', $member->id)
                    ->where('status', 'issued')
                    ->first()
                    ?->revokeFromVisitor('Group lead checked out via kiosk.');
            });

        $this->screen = 'success';
    }

    public function render(): View
    {
        return view('clientservice::livewire.visitor-check-out')
            ->layout('clientservice::layouts.kiosk', [
                'companyName' => $this->companyName,
                'title'       => 'Visitor Check-Out — ' . $this->companyName,
            ]);
    }
}