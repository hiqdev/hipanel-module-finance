<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

enum CostpriceType: string
{
    case hw = 'hw_split';
    case traff = 'traff_split';
}
