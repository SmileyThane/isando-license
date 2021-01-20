<?php

namespace App\Policies;

use App\User;
use App\Query;
use Illuminate\Auth\Access\HandlesAuthorization;

class QueryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the query.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-query');
    }

    /**
     * Determine whether the user can view the query.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-query');
    }

    /**
     * Determine whether the user can update the query.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-query');
    }

    /**
     * Determine whether the user can delete the query.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-query');
    }
}
