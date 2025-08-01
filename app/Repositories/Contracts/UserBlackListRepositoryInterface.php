<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface UserBlackListRepositoryInterface extends BaseRepositoryInterface
{
    // Add any additional methods specific to UserBlackList repository if needed

    /**
     * Toggle user blacklist record: if exists delete, else create.
     *
     * @param array $data
     * @return bool
     */
    public function toggle(array $data): bool;
}
