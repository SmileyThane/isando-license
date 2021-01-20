<?php

namespace App\Policies;

use App\User;
use App\Plan;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can fetch pre-requisites for plan.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function preRequisite(User $user)
    {
        return ($user->can('create-plan') || $user->can('edit-plan'));
    }

    /**
     * Determine whether the user can list all the plan.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-plan');
    }

    /**
     * Determine whether the user can view the plan.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-plan');
    }

    /**
     * Determine whether the user can create plans.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-plan');
    }

    /**
     * Determine whether the user can update the plan.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-plan');
    }

    /**
     * Determine whether the user can delete the plan.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-plan');
    }
}
