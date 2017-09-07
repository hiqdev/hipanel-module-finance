<?php

namespace hipanel\modules\finance\cart;

/**
 * Interface PositionPurchasabilityValidatorInterface
 */
interface PositionPurchasabilityValidatorInterface
{
    /**
     * @param $positions AbstractCartPosition[]
     * @return mixed
     */
    public function validate($positions);
}
