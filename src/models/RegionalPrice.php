<?php declare(strict_types=1);

namespace hipanel\modules\finance\models;

/**
 * One plan row that stands for the same metric priced per region (e.g. ASIA, EUROPE and USA traffic overuse).
 * It carries the attributes of the first regional price, with the Total type of the metric as its type.
 */
class RegionalPrice extends Price
{
    /** @var array<string, Price> region code => price */
    public array $members = [];

    /** @return int[] */
    public function memberIds(): array
    {
        return array_values(array_map(static fn(Price $price) => $price->id, $this->members));
    }
}
