<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface FavoriteRepositoryInterface extends BaseRepositoryInterface
{
    // Add any additional methods specific to Favorite repository if needed

    /**
     * Toggle favorite record: if exists delete, else create.
     *
     * @param array $data
     * @return bool
     */
    public function toggle(array $data): bool;
}
