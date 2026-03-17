<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Certification;
use Modules\HR\Models\Employee;
use Modules\HR\Filament\Resources\Staff\MyCertificationResource\Schemas\CertificationForm;
use Modules\HR\Filament\Resources\Staff\MyCertificationResource\Schemas\CertificationInfolist;
use Modules\HR\Filament\Resources\Staff\MyCertificationResource\Tables\CertificationsTable;

class MyCertificationResource extends StaffSelfServiceResource
{
    protected static ?string $model = Certification::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'My Certifications';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'my-certifications';

    // Certification has no company() relationship — scoped via employee_id in getEloquentQuery()
    protected static bool $isScopedToTenant = false;

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
            ->orderByDesc('issue_date');
    }

    public static function form(Schema $schema): Schema
    {
        return CertificationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CertificationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \Modules\HR\Filament\Resources\Staff\MyCertificationResource\Pages\ListMyCertifications::route('/'),
            'create' => \Modules\HR\Filament\Resources\Staff\MyCertificationResource\Pages\CreateMyCertification::route('/create'),
            'view'   => \Modules\HR\Filament\Resources\Staff\MyCertificationResource\Pages\ViewMyCertification::route('/{record}'),
            'edit'   => \Modules\HR\Filament\Resources\Staff\MyCertificationResource\Pages\EditMyCertification::route('/{record}/edit'),
        ];
    }
}
