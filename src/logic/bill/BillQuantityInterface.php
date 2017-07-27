<?php

namespace hipanel\modules\finance\logic\bill;

interface BillQuantityInterface
{
    /**
     * Returns textual user friendly representation of the quantity.
     * E.g. 20 days, 30 GB, 1 year
     * @return string
     */
    public function getText();

    /**
     * Returns numeric to be saved in DB.
     * @return float|int
     */
    public function getValue();

    /**
     * Returns numeric user friendly representation of the quantity.
     * @return float|int
     */
    public function getClientValue();
}
