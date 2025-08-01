<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function proceedImage($file, string|null $oldFileName = null): bool|string;
    public function proceedImageDelete($file, string|null $oldFileName = null);
}
