<?php

namespace App\Policies;

use App\User;
use App\Subscriber;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the subscriber.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-subscriber');
    }

    /**
     * Determine whether the user can view the subscriber.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-subscriber');
    }

    /**
     * Determine whether the user can update the subscriber.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-subscriber');
    }

    /**
     * Determine whether the user can delete the subscriber.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-subscriber');
    }
}
