<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
interface AuthRepositoryInterface
{

    public function update(User $user, array $data): bool;
    public function updateInfo(User $user, array $data): bool;

}
