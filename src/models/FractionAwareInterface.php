<?php declare(strict_types=1);

namespace hipanel\modules\finance\models;

interface FractionAwareInterface extends HasTimeAttributeInterface
{
    public function getFractionOfMonth();

    public function getQuantity();
}
