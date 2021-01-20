<?php

namespace App\Policies;

use App\User;
use App\Instance;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the instance.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-instance');
    }

    /**
     * Determine whether the user can view the instance.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-instance');
    }

    /**
     * Determine whether the user can update the instance.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-instance');
    }

    /**
     * Determine whether the user can delete the instance.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-instance');
    }
}
