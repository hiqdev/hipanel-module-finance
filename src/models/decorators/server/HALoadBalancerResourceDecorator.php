<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class HALoadBalancerResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel.finance.resource', 'Load Balancer Hight-Available capacity unit');
    }

    public function displayUnit()
    {
        return Yii::t('hipanel', 'Items');
    }

    public function displayValue()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => $this->getPrepaidQuantity()]);
    }

    public function toUnit(): string
    {
        return 'items';
    }
}
