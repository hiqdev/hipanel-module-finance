<?php

declare(strict_types=1);


namespace hipanel\modules\finance\models;

interface RepresentablePrice
{
    public function getUnitLabel(): ?string;

    public function getFormulaLines(): array;
}
