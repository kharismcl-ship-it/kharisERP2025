<?php

namespace Modules\HR\Filament\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\HR\Models\Employee;
use Modules\HR\Models\OnboardingTask;

class MyOnboardingPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static string|\UnitEnum|null $navigationGroup = 'HR';
    protected static ?int $navigationSort = 70;
    protected static ?string $navigationLabel = 'My Onboarding';
    protected static ?string $slug = 'my-onboarding';

    protected string $view = 'hr::filament.pages.my-onboarding';

    public array $tasks = [];

    public function mount(): void
    {
        $this->loadTasks();
    }

    private function loadTasks(): void
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)->first();

        if (! $employee) {
            $this->tasks = [];
            return;
        }

        $this->tasks = OnboardingTask::scopeForEmployee(
            OnboardingTask::where('company_id', $companyId)->orderBy('sort_order'),
            $employee->id
        )->get()->toArray();
    }

    public function complete(int $taskId): void
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)->first();

        if (! $employee) {
            return;
        }

        $task = OnboardingTask::where('id', $taskId)
            ->where('employee_id', $employee->id)
            ->first();

        if ($task && $task->status !== 'completed') {
            $task->update([
                'status'                   => 'completed',
                'completed_at'             => now(),
                'completed_by_employee_id' => $employee->id,
            ]);

            Notification::make()->success()->title('Task marked complete!')->send();
            $this->loadTasks();
        }
    }
}