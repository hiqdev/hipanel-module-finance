<?php

namespace hipanel\modules\finance\grid\presenters\price;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Class ModelGroupPricePresenter
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ModelGroupPricePresenter extends PricePresenter
{
    /**
     * @param \hipanel\modules\finance\models\ModelGroupPrice $price
     * @return string
     */
    public function renderPrice($price): string
    {
        $formatter = Yii::$app->formatter;

        $result = [
            Html::tag('strong', $formatter->asCurrency($price->price, $price->currency))
        ];
        foreach ($price->subprices as $currencyCode => $amount) {
            try {
                $result[] = $formatter->asCurrency($amount, $currencyCode);
            } catch (InvalidConfigException $e) {
                $result[] = $amount . ' ' . $currencyCode;
            }
        }

        return implode('&nbsp;&mdash;&nbsp;', $result);
    }
}
