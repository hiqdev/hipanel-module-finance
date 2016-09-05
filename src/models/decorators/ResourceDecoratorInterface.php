<?php

namespace hipanel\modules\finance\models\decorators;



interface ResourceDecoratorInterface
{
    public function getPrepaidQuantity();

    public function getOverusePrice();

    public function displayTitle();

    public function displayUnit();

    public function displayOverusePrice();

    public function displayPrepaidAmount();

    public function prepaidAmountType();
}
