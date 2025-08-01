<?php

namespace App\Models;

use App\Interfaces\AutoFilterable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CURDTrait;
use App\Traits\IsAutoFilterable;

class GeneralModel extends Model implements AutoFilterable
{
    use CURDTrait;
    use IsAutoFilterable;
}
