<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use Yii;

/**
 * aCDN traffic regions, as returned in the `region` field of charges and bills.
 */
final class AcdnRegion
{
    /**
     * @return array<string, string> region code => translated label
     */
    public static function labels(): array
    {
        return [
            'asia' => Yii::t('hipanel:finance', 'Asia'),
            'europe' => Yii::t('hipanel:finance', 'Europe'),
            'usa' => Yii::t('hipanel:finance', 'USA'),
        ];
    }

    public static function label(?string $region): string
    {
        return $region === null ? '' : (self::labels()[$region] ?? $region);
    }
}
