<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BusinessSettingRepositoryInterface
{
    public function find(string $id): ?Model;
    public function all(): array;
    public function update(string $id, array $data): bool;
    public function updateAboutUsImage($image);
}
