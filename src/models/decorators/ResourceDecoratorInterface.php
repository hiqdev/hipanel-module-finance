<?php

namespace hipanel\modules\finance\models\decorators;

interface ResourceDecoratorInterface
{
    public function getPrepaidAmount();

    public function getOverusePrice();

    public function displayTitle();

    public function displayUnit();

    public function displayValue();

    public function displayOverusePrice();

    public function displayPrepaidAmount();
}
