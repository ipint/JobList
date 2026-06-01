<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use MWGuerra\FileManager\Policies\FileSystemItemPolicy as BasePolicy;

class FileSystemItemPolicy extends BasePolicy
{
    protected function canMedia(?Authenticatable $user, string $action): bool
    {
        return $user instanceof User && $user->canAccess('media', $action);
    }

    public function viewAny(?Authenticatable $user): bool
    {
        return $this->canMedia($user, 'view');
    }

    public function view(?Authenticatable $user, $item): bool
    {
        return $this->canMedia($user, 'view');
    }

    public function create(?Authenticatable $user): bool
    {
        return $this->canMedia($user, 'create');
    }

    public function update(?Authenticatable $user, $item): bool
    {
        return $this->canMedia($user, 'edit');
    }

    public function delete(?Authenticatable $user, $item): bool
    {
        return $this->canMedia($user, 'delete');
    }

    public function deleteAny(?Authenticatable $user): bool
    {
        return $this->canMedia($user, 'delete');
    }

    public function download(?Authenticatable $user, $item): bool
    {
        return $this->canMedia($user, 'view');
    }
}
