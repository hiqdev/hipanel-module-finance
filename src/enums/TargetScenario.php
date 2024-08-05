<?php declare(strict_types=1);

namespace hipanel\modules\finance\enums;

enum TargetScenario: string
{
    case CHANGE_PLAN = 'change-plan';
    case CLOSE_SALE = 'close-sale';
    case SALE = 'sale';
}
