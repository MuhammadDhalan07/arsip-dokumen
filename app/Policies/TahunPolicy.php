<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Tahun;
use Illuminate\Auth\Access\HandlesAuthorization;

class TahunPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Tahun');
    }

    public function view(AuthUser $authUser, Tahun $tahun): bool
    {
        return $authUser->can('View:Tahun');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Tahun');
    }

    public function update(AuthUser $authUser, Tahun $tahun): bool
    {
        return $authUser->can('Update:Tahun');
    }

    public function delete(AuthUser $authUser, Tahun $tahun): bool
    {
        return $authUser->can('Delete:Tahun');
    }

    public function restore(AuthUser $authUser, Tahun $tahun): bool
    {
        return $authUser->can('Restore:Tahun');
    }

    public function forceDelete(AuthUser $authUser, Tahun $tahun): bool
    {
        return $authUser->can('ForceDelete:Tahun');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Tahun');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Tahun');
    }

    public function replicate(AuthUser $authUser, Tahun $tahun): bool
    {
        return $authUser->can('Replicate:Tahun');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Tahun');
    }

}