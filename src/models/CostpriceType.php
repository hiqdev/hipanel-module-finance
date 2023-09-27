<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

enum CostpriceType: string
{
    case admin = 'admin_split';
    case colocation = 'colocation_split';
    case ip = 'ip_split';
    case hw = 'hw_split';
    case nrc = 'nrc_split';
    case salaries = 'salaries_split';
}
