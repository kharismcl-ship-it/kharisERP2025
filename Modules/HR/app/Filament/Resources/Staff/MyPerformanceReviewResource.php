<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\PerformanceReview;
use Modules\HR\Filament\Resources\Staff\MyPerformanceReviewResource\Schemas\PerformanceReviewInfolist;
use Modules\HR\Filament\Resources\Staff\MyPerformanceReviewResource\Tables\PerformanceReviewsTable;

class MyPerformanceReviewResource extends StaffSelfServiceResource
{
    protected static ?string $model = PerformanceReview::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $navigationLabel = 'My Reviews';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 55;

    protected static ?string $slug = 'my-performance-reviews';

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()
            ->where('employee_id', $employee->id)
            ->orderByDesc('created_at');
    }

    public static function infolist(Schema $schema): Schema
    {
        return PerformanceReviewInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PerformanceReviewsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\HR\Filament\Resources\Staff\MyPerformanceReviewResource\Pages\ListMyPerformanceReviews::route('/'),
            'view'  => \Modules\HR\Filament\Resources\Staff\MyPerformanceReviewResource\Pages\ViewMyPerformanceReview::route('/{record}'),
        ];
    }
}
