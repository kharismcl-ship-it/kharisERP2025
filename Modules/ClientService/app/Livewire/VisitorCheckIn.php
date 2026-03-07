<?php

namespace Modules\ClientService\Livewire;

use App\Models\Company;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QROutputInterface;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
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
use Modules\ClientService\Models\CsVisitorBadge;
use Modules\ClientService\Models\CsVisitorProfile;

class VisitorCheckIn extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    #[Locked]
    public int $companyId;

    public string $companyName = '';
    public string $companySlug = '';

    /** welcome | form | success */
    public string $screen = 'welcome';

    public ?string $checkedInName   = null;
    public ?string $badgeCode       = null;
    public ?string $badgeQrDataUri  = null;
    public ?string $profileQrDataUri = null;

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(Company $company, ?string $profileToken = null): void
    {
        $this->companyId   = $company->id;
        $this->companyName = $company->name;
        $this->companySlug = $company->slug;

        $prefill = [
            'company_id'  => $company->id,
            'check_in_at' => now()->toDateTimeString(),
        ];

        if ($profileToken) {
            $profile = CsVisitorProfile::withoutGlobalScopes()
                ->where('profile_token', $profileToken)
                ->where('company_id', $company->id)
                ->first();

            if ($profile) {
                $prefill = array_merge($prefill, [
                    'visitor_type'         => 'returning',
                    'full_name'            => $profile->full_name,
                    'phone'                => $profile->phone,
                    'email'                => $profile->email,
                    'id_type'              => $profile->id_type,
                    'id_number'            => $profile->id_number,
                    'organization'         => $profile->organization,
                    'communication_opt_in' => (bool) $profile->communication_opt_in,
                ]);
                // Prefill photo and signature if stored on the profile
                if ($profile->photo_path) {
                    $prefill['photo_path'] = $profile->photo_path;
                }
                if ($profile->check_in_signature) {
                    $prefill['check_in_signature'] = $profile->check_in_signature;
                }

                $this->screen = 'form';
            }
        }

        $this->form->fill($prefill);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(CsVisitor::class)
            ->components([
                Wizard::make(CsVisitorResource::wizardSteps(kiosk: true))
                    ->columnSpanFull()
                    ->submitAction(new HtmlString(Blade::render(
                        '<x-filament::button wire:click="submit" type="button" size="xl" icon="heroicon-o-check-circle">Complete Check-In</x-filament::button>'
                    ))),
            ])
            ->statePath('data');
    }

    public function startCheckIn(string $visitorType): void
    {
        $this->form->fill([
            'visitor_type' => $visitorType,
            'company_id'   => $this->companyId,
            'check_in_at'  => now()->toDateTimeString(),
        ]);

        $this->screen = 'form';
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $data['company_id'] = $this->companyId;
        $data['check_in_at'] = $data['check_in_at'] ?? now()->toDateTimeString();

        // Extract group members before creating the main visitor record
        $groupMembers = $data['group_members'] ?? [];
        unset($data['group_members'], $data['_has_group']);

        $optIn = (bool) ($data['communication_opt_in'] ?? false);

        // Upsert visitor profile (match on company + phone)
        if (! empty($data['phone'])) {
            $profile = CsVisitorProfile::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $this->companyId, 'phone' => $data['phone']],
                [
                    'full_name'            => $data['full_name'],
                    'email'                => $data['email'] ?? null,
                    'id_type'              => $data['id_type'] ?? null,
                    'id_number'            => $data['id_number'] ?? null,
                    'organization'         => $data['organization'] ?? null,
                    'photo_path'           => $data['photo_path'] ?? null,
                    'check_in_signature'   => $data['check_in_signature'] ?? null,
                    'communication_opt_in' => $optIn,
                ]
            );
        } else {
            $profile = CsVisitorProfile::create([
                'company_id'           => $this->companyId,
                'full_name'            => $data['full_name'],
                'email'                => $data['email'] ?? null,
                'id_type'              => $data['id_type'] ?? null,
                'id_number'            => $data['id_number'] ?? null,
                'organization'         => $data['organization'] ?? null,
                'photo_path'           => $data['photo_path'] ?? null,
                'check_in_signature'   => $data['check_in_signature'] ?? null,
                'communication_opt_in' => $optIn,
            ]);
        }

        $data['visitor_profile_id']   = $profile->id;
        $data['communication_opt_in'] = $optIn;

        // Create the main visitor record
        $visitor = CsVisitor::create($data);

        // Auto-assign next available badge for this company
        $badge = CsVisitorBadge::available()
            ->where('company_id', $this->companyId)
            ->orderBy('badge_code')
            ->first()
            // Fallback: any available badge if company has none assigned yet
            ?? CsVisitorBadge::available()->orderBy('badge_code')->first();

        if ($badge) {
            $badge->issueToVisitor($visitor, issuedByUserId: null);
            $visitor->refresh();
        }

        // Create individual visitor records for each group member
        foreach ($groupMembers as $member) {
            if (blank($member['full_name'] ?? '')) {
                continue;
            }

            // Upsert group member profile
            $memberProfile = ! empty($member['phone'])
                ? CsVisitorProfile::withoutGlobalScopes()->updateOrCreate(
                    ['company_id' => $this->companyId, 'phone' => $member['phone']],
                    [
                        'full_name'            => $member['full_name'],
                        'email'                => $member['email'] ?? null,
                        'communication_opt_in' => (bool) ($member['communication_opt_in'] ?? false),
                    ]
                )
                : CsVisitorProfile::create([
                    'company_id'           => $this->companyId,
                    'full_name'            => $member['full_name'],
                    'email'                => $member['email'] ?? null,
                    'communication_opt_in' => (bool) ($member['communication_opt_in'] ?? false),
                ]);

            $memberVisitor = CsVisitor::create([
                'company_id'             => $this->companyId,
                'visitor_profile_id'     => $memberProfile->id,
                'group_lead_visitor_id'  => $visitor->id,
                'full_name'              => $member['full_name'],
                'phone'                  => $member['phone'] ?? null,
                'email'                  => $member['email'] ?? null,
                'purpose_of_visit'       => $visitor->purpose_of_visit,
                'host_employee_id'       => $visitor->host_employee_id,
                'department_id'          => $visitor->department_id,
                'check_in_at'            => $visitor->check_in_at,
                'communication_opt_in'   => (bool) ($member['communication_opt_in'] ?? false),
            ]);

            // Assign a badge to each group member too
            $memberBadge = CsVisitorBadge::available()
                ->where('company_id', $this->companyId)
                ->orderBy('badge_code')
                ->first()
                ?? CsVisitorBadge::available()->orderBy('badge_code')->first();

            if ($memberBadge) {
                $memberBadge->issueToVisitor($memberVisitor, issuedByUserId: null);
            }
        }

        // Generate QR codes
        $qr = new QRCode(new QROptions([
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'scale'      => 5,
        ]));

        $this->badgeQrDataUri = $qr->render(
            route('clientservice.visitor-check-out', [
                'company'      => $this->companySlug,
                'checkInToken' => $visitor->check_in_token,
            ])
        );

        $this->profileQrDataUri = $qr->render(
            route('clientservice.visitor-check-in.returning', [
                'company'      => $this->companySlug,
                'profileToken' => $profile->profile_token,
            ])
        );

        $this->checkedInName = $visitor->full_name;
        $this->badgeCode     = $visitor->badge_number;
        $this->screen        = 'success';
    }

    public function resetKiosk(): void
    {
        $this->screen           = 'welcome';
        $this->checkedInName    = null;
        $this->badgeCode        = null;
        $this->badgeQrDataUri   = null;
        $this->profileQrDataUri = null;
        $this->data             = [];

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