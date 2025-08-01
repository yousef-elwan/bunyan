<?php

namespace App\Repositories\Contracts;


interface AboutUsRepositoryInterface
{

    /**
     * update model.
     *
     * @param array $data Data value
     * @return bool
     */
    public function store(array $data);

    public function proceedImage($file, string|null $oldFileName = null): bool|string;

    public function proceedImageDelete($file, string|null $oldFileName = null);
}
