<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Repositories\Contracts\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function updatePassword(User $user, string $newPassword): bool;

    public function updateInfo(User $user, array $data): bool;

    public function checkOldPassword(User $user, string $oldPassword): bool;

    public function proceedImage($file, string|null $oldFileName = null): bool|string;

    public function proceedImageDelete($file, string|null $oldFileName = null);
}
