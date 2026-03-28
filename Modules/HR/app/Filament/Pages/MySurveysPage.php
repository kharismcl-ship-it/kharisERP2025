<?php

namespace Modules\HR\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Survey;
use Modules\HR\Models\SurveyAnswer;
use Modules\HR\Models\SurveyResponse;

class MySurveysPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static string|\UnitEnum|null $navigationGroup = 'HR';
    protected static ?int $navigationSort = 72;
    protected static ?string $navigationLabel = 'Surveys';
    protected static ?string $slug = 'my-surveys';

    protected string $view = 'hr::filament.pages.my-surveys';

    public ?int $activeSurveyId = null;
    public array $answers = [];
    public array $availableSurveys = [];

    public function mount(): void
    {
        $this->loadSurveys();
    }

    private function getEmployee(): ?Employee
    {
        $companyId = Filament::getTenant()?->id;
        return Employee::where('user_id', auth()->id())->where('company_id', $companyId)->first();
    }

    private function loadSurveys(): void
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = $this->getEmployee();

        $query = Survey::where('company_id', $companyId)
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));

        // Exclude surveys already responded to (if not anonymous or if employee exists)
        if ($employee) {
            $respondedIds = SurveyResponse::where('employee_id', $employee->id)
                ->whereNotNull('submitted_at')
                ->pluck('survey_id');
            $query->whereNotIn('id', $respondedIds);
        }

        $this->availableSurveys = $query->with('questions')->get()->toArray();
    }

    public function openSurvey(int $surveyId): void
    {
        $this->activeSurveyId = $surveyId;
        $this->answers        = [];
    }

    public function submitSurvey(): void
    {
        $employee = $this->getEmployee();

        $survey = Survey::with('questions')->find($this->activeSurveyId);
        if (! $survey) {
            return;
        }

        // Validate required questions
        foreach ($survey->questions as $question) {
            if ($question->is_required && empty($this->answers[$question->id])) {
                Notification::make()->danger()->title('Please answer all required questions.')->send();
                return;
            }
        }

        $response = SurveyResponse::create([
            'survey_id'    => $survey->id,
            'employee_id'  => $survey->is_anonymous ? null : $employee?->id,
            'submitted_at' => now(),
        ]);

        foreach ($this->answers as $questionId => $answer) {
            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'survey_question_id' => $questionId,
                'answer'             => is_array($answer) ? implode(', ', $answer) : $answer,
            ]);
        }

        $this->activeSurveyId = null;
        $this->answers        = [];
        $this->loadSurveys();

        Notification::make()->success()->title('Thank you! Your response has been recorded.')->send();
    }

    public function cancelSurvey(): void
    {
        $this->activeSurveyId = null;
        $this->answers        = [];
    }
}