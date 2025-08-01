<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface PropertyRepositoryInterface extends BaseRepositoryInterface
{
    public function proceedImage($file, string $propertyId, string|null $oldFileName = null): bool|string;
    public function proceedImageDelete($file, string $propertyId, string|null $oldFileName = null);
    public function proceedImageDeleteByName(string $propertyId, string $name);
}
