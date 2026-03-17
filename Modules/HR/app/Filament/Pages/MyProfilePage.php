<?php

namespace Modules\HR\Filament\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Modules\HR\Models\Employee;

class MyProfilePage extends Page
{
    protected string $view = 'hr::filament.pages.my-profile';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?int $navigationSort = 5;

    public ?Employee $employee = null;

    public string $phone           = '';
    public string $address         = '';
    public string $emergency_contact_name  = '';
    public string $emergency_contact_phone = '';

    public function mount(): void
    {
        $companyId      = Filament::getTenant()?->id;
        $this->employee = Employee::with(['department', 'jobPosition', 'user'])
            ->where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if ($this->employee) {
            $this->phone                   = $this->employee->phone ?? '';
            $this->address                 = $this->employee->address ?? '';
            $this->emergency_contact_name  = $this->employee->emergency_contact_name ?? '';
            $this->emergency_contact_phone = $this->employee->emergency_contact_phone ?? '';
        }
    }

    public function updateContact(): void
    {
        if (! $this->employee) {
            return;
        }

        $this->employee->update([
            'phone'                   => $this->phone,
            'address'                 => $this->address,
            'emergency_contact_name'  => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
        ]);

        Notification::make()
            ->title('Contact details updated.')
            ->success()
            ->send();
    }
}
