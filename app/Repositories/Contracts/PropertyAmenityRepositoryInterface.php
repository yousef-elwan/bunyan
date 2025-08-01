<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface PropertyAmenityRepositoryInterface extends BaseRepositoryInterface
{
    // Add any additional methods specific to PropertyAmenity repository if needed

    /**
     * Toggle property amenity record: if exists delete, else create.
     *
     * @param array $data
     * @return bool
     */
    public function toggle(array $data): bool;
}
