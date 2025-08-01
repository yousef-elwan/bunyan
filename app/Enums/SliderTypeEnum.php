<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum SliderTypeEnum: string
{
    use Values;

    case popup = 'popup';
    case header = 'header';
    case footer = 'footer';
    case offerImage = 'offer-image';
}
