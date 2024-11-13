<?php declare(strict_types=1);

namespace hipanel\modules\finance\models;

interface FractionAwareInterface
{
    public function getFractionOfMonth();

    public function getTime();

    public function getQuantity();
}
