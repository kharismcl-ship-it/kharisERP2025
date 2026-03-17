<?php

declare(strict_types=1);

namespace Modules\Hostels\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Modules\Hostels\Models\HostelMovieRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelMovieRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HostelMovieRequest');
    }

    public function view(AuthUser $authUser, HostelMovieRequest $hostelMovieRequest): bool
    {
        return $authUser->can('View:HostelMovieRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HostelMovieRequest');
    }

    public function update(AuthUser $authUser, HostelMovieRequest $hostelMovieRequest): bool
    {
        return $authUser->can('Update:HostelMovieRequest');
    }

    public function delete(AuthUser $authUser, HostelMovieRequest $hostelMovieRequest): bool
    {
        return $authUser->can('Delete:HostelMovieRequest');
    }

    public function restore(AuthUser $authUser, HostelMovieRequest $hostelMovieRequest): bool
    {
        return $authUser->can('Restore:HostelMovieRequest');
    }

    public function forceDelete(AuthUser $authUser, HostelMovieRequest $hostelMovieRequest): bool
    {
        return $authUser->can('ForceDelete:HostelMovieRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HostelMovieRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HostelMovieRequest');
    }

    public function replicate(AuthUser $authUser, HostelMovieRequest $hostelMovieRequest): bool
    {
        return $authUser->can('Replicate:HostelMovieRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HostelMovieRequest');
    }

}