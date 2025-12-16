<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Rincian;
use Illuminate\Auth\Access\HandlesAuthorization;

class RincianPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Rincian');
    }

    public function view(AuthUser $authUser, Rincian $rincian): bool
    {
        return $authUser->can('View:Rincian');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Rincian');
    }

    public function update(AuthUser $authUser, Rincian $rincian): bool
    {
        return $authUser->can('Update:Rincian');
    }

    public function delete(AuthUser $authUser, Rincian $rincian): bool
    {
        return $authUser->can('Delete:Rincian');
    }

    public function restore(AuthUser $authUser, Rincian $rincian): bool
    {
        return $authUser->can('Restore:Rincian');
    }

    public function forceDelete(AuthUser $authUser, Rincian $rincian): bool
    {
        return $authUser->can('ForceDelete:Rincian');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Rincian');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Rincian');
    }

    public function replicate(AuthUser $authUser, Rincian $rincian): bool
    {
        return $authUser->can('Replicate:Rincian');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Rincian');
    }

}