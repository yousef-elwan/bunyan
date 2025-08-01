<?php

namespace App\Services;

use App\Repositories\Contracts\BusinessSettingRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class BusinessSettingService
{
    public function __construct(
        protected BusinessSettingRepositoryInterface $repo
    ) {}


}
