<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;

class PersonPolicy
{
    /**
     * Determine if the user can view any people.
     */
    public function viewAny(User $user): bool
    {
        // All active users can view the tree
        return $user->isActive();
    }

    /**
     * Determine if the user can view a specific person.
     */
    public function view(User $user, Person $person): bool
    {
        // All active users can view any person
        return $user->isActive();
    }

    /**
     * Determine if the user can create people.
     */
    public function create(User $user): bool
    {
        // Admins can always create
        if ($user->isAdmin()) {
            return true;
        }

        // Editors can create (they'll be restricted to adding their own children in the controller)
        if ($user->isEditor()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can update a person.
     */
    public function update(User $user, Person $person): bool
    {
        return $user->canEdit($person);
    }

    /**
     * Determine if the user can delete a person.
     */
    public function delete(User $user, Person $person): bool
    {
        return $user->canDelete($person);
    }
}
