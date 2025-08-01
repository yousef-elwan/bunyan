<?php

namespace App\Enums;

use ArchTech\Enums\Names;
use ArchTech\Enums\From;

enum FilterFnsEnum
{
    use From, Names;

    case between;
    case betweenInclusive;
    case contains;
    case empty;
    case endsWith;
    case equals;
    case fuzzy;
    case greaterThan;
    case greaterThanOrEqualTo;
    case lessThan;
    case lessThanOrEqualTo;
    case notEmpty;
    case notEquals;
    case startsWith;
    case includesString;
    case includesStringSensitive;
    case equalsString;
    case arrIncludes;
    case arrIncludesAll;
    case arrIncludesSome;
    case weakEquals;
    case inNumberRange;

    case dayEquals;

    case in;
    case notIn;
    case notContains;
    case notStartsWith;
    case notEndsWith;
}
