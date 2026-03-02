<?php

declare(strict_types=1);

namespace Modules\ManufacturingPaper\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\ManufacturingPaper\Models\MpPaperGrade;
use Illuminate\Auth\Access\HandlesAuthorization;

class MpPaperGradePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MpPaperGrade');
    }

    public function view(AuthUser $authUser, MpPaperGrade $mpPaperGrade): bool
    {
        return $authUser->can('View:MpPaperGrade');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MpPaperGrade');
    }

    public function update(AuthUser $authUser, MpPaperGrade $mpPaperGrade): bool
    {
        return $authUser->can('Update:MpPaperGrade');
    }

    public function delete(AuthUser $authUser, MpPaperGrade $mpPaperGrade): bool
    {
        return $authUser->can('Delete:MpPaperGrade');
    }

    public function restore(AuthUser $authUser, MpPaperGrade $mpPaperGrade): bool
    {
        return $authUser->can('Restore:MpPaperGrade');
    }

    public function forceDelete(AuthUser $authUser, MpPaperGrade $mpPaperGrade): bool
    {
        return $authUser->can('ForceDelete:MpPaperGrade');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MpPaperGrade');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MpPaperGrade');
    }

    public function replicate(AuthUser $authUser, MpPaperGrade $mpPaperGrade): bool
    {
        return $authUser->can('Replicate:MpPaperGrade');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MpPaperGrade');
    }

}