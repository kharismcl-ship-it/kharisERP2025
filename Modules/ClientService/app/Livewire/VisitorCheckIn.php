<?php

namespace Modules\ClientService\Livewire;

use App\Models\Company;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Modules\ClientService\Filament\Resources\CsVisitorResource;
use Modules\ClientService\Models\CsVisitor;

/**
 * Public-facing visitor kiosk check-in component.
 *
 * Mounted as a full-page Livewire route. Reuses CsVisitorResource::wizardSteps()
 * as the single source of truth for form fields.
 *
 * Tenant context: company is resolved from the URL slug, company_id is
 * injected into the form state and forced on save — no Filament tenant context
 * is present in this public route.
 */
class VisitorCheckIn extends Component implements HasForms
{
    use InteractsWithForms;

    #[Locked]
    public int $companyId;

    public string $companyName = '';

    /** welcome | form | success */
    public string $screen = 'welcome';

    public ?string $checkedInName = null;

    public ?string $badgeNumber = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(Company $company): void
    {
        $this->companyId  = $company->id;
        $this->companyName = $company->name;

        $this->form->fill([
            'company_id'   => $company->id,
            'check_in_at'  => now()->toDateTimeString(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make(CsVisitorResource::wizardSteps())
                    ->columnSpanFull()
                    ->submitAction(new HtmlString(Blade::render(
                        '<x-filament::button wire:click="submit" type="button" size="xl" icon="heroicon-o-check-circle">Complete Check-In</x-filament::button>'
                    ))),
            ])
            ->statePath('data');
    }

    /**
     * Triggered by the welcome-screen cards.
     * Pre-fills visitor_type so Step 1 of the wizard is already configured.
     */
    public function startCheckIn(string $visitorType): void
    {
        $this->form->fill([
            'visitor_type' => $visitorType,
            'company_id'   => $this->companyId,
            'check_in_at'  => now()->toDateTimeString(),
        ]);

        $this->screen = 'form';
    }

    /**
     * Called by the wizard submit button.
     * Forces company_id from the URL — never trusts form input for tenancy.
     */
    public function submit(): void
    {
        $data = $this->form->getState();

        // Enforce the company from the URL regardless of what the form contains.
        $data['company_id'] = $this->companyId;

        $visitor = CsVisitor::create($data);

        $this->checkedInName = $visitor->full_name;
        $this->badgeNumber   = $visitor->badge_number;
        $this->screen        = 'success';
    }

    /**
     * Resets the kiosk to the welcome screen.
     * Called by the success screen's "New Check-In" button and the auto-timer.
     */
    public function resetKiosk(): void
    {
        $this->screen        = 'welcome';
        $this->checkedInName = null;
        $this->badgeNumber   = null;
        $this->data          = [];

        $this->form->fill([
            'company_id'  => $this->companyId,
            'check_in_at' => now()->toDateTimeString(),
        ]);
    }

    public function backToWelcome(): void
    {
        $this->screen = 'welcome';
        $this->data   = [];

        $this->form->fill([
            'company_id'  => $this->companyId,
            'check_in_at' => now()->toDateTimeString(),
        ]);
    }

    public function render(): View
    {
        return view('clientservice::livewire.visitor-check-in')
            ->layout('clientservice::layouts.kiosk', [
                'companyName' => $this->companyName,
                'title'       => 'Visitor Check-In — ' . $this->companyName,
            ]);
    }
}
