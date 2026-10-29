<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\RegionalPrice;
use hiqdev\billing\registry\Domain\Finance\Enum\PriceType;

/**
 * Collapses the regional prices of the same metric into one row; other prices stay as they are.
 */
final class RegionalPriceGrouper
{
    /**
     * @param Price[] $prices
     * @return Price[]
     */
    public static function group(array $prices): array
    {
        $result = [];
        $groups = [];
        foreach ($prices as $key => $price) {
            $region = self::regionOf($price);
            if ($region === null) {
                $result[$key] = $price;
                continue;
            }
            $groupKey = self::kindOf($price) . ',' . self::metricOf($price, $region) . '#' . ($price->object_id ?? '');
            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = self::createGroup($price, $region);
                $result[$key] = $groups[$groupKey];
            }
            $groups[$groupKey]->members[$region] = $price;
        }

        return $result;
    }

    private static function regionOf(Price $price): ?string
    {
        $subtype = self::subtypeOf($price);

        return $subtype === null ? null : PriceType::tryFromName($subtype)?->region()?->name;
    }

    private static function subtypeOf(Price $price): ?string
    {
        return str_contains((string)$price->type, ',') ? $price->getSubtype() : null;
    }

    private static function kindOf(Price $price): string
    {
        return explode(',', (string)$price->type)[0];
    }

    private static function metricOf(Price $price, string $region): string
    {
        return str_replace("_$region", '', self::subtypeOf($price));
    }

    private static function createGroup(Price $first, string $region): RegionalPrice
    {
        $group = new RegionalPrice();
        $group->setAttributes($first->getAttributes(), false);
        $group->type = self::kindOf($first) . ',' . self::metricOf($first, $region);
        foreach (['object', 'plan'] as $relation) {
            if ($first->isRelationPopulated($relation)) {
                $group->populateRelation($relation, $first->$relation);
            }
        }

        return $group;
    }
}
