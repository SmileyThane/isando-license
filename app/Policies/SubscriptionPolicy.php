<?php

namespace App\Policies;

use App\User;
use App\Subscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the subscription.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-subscription');
    }

    /**
     * Determine whether the user can view the subscription.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-subscription');
    }

    /**
     * Determine whether the user can print the subscription invoice.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function print(User $user)
    {
        return $user->can('list-subscription');
    }

    /**
     * Determine whether the user can generate PDF of the subscription invoice.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function pdf(User $user)
    {
        return $user->can('list-subscription');
    }

    /**
     * Determine whether the user can update the subscription.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-subscription');
    }

    /**
     * Determine whether the user can delete the subscription.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-subscription');
    }
}
