<?php

namespace hipanel\modules\finance\grid\presenters\price;

use hipanel\modules\finance\models\Price;
use Yii;

/**
 * Class PricePresenter contains methods that present price properties.
 * You can override this class to add custom presentations support.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PricePresenter
{
    /**
     * @param Price $price
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderPrice($price): string
    {
        return Yii::$app->formatter->asCurrency($price->price, $price->currency)
            . '/' . Yii::t('hipanel.finance.price', $price->getUnitLabel());
    }
}
