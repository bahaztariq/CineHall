<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class FilmPolicy
{
    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }

        return Response::deny('Only admins can manage films.');
    }
}