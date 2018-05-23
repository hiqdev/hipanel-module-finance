<?php

namespace hipanel\modules\finance\grid\presenters\price;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Class TemplatePricePresenter
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TemplatePricePresenter extends PricePresenter
{
    /**
     * @param \hipanel\modules\finance\models\TemplatePrice $price
     * @return string
     */
    public function renderPrice($price): string
    {
        $formatter = Yii::$app->formatter;

        $unit = '';
        if ($price->getUnitLabel()) {
            $unit = ' ' . Yii::t('hipanel:finance', 'per {unit}', ['unit' => $price->getUnitLabel()]);
        }

        $result = [
            Html::tag('strong', $formatter->asCurrency($price->price, $price->currency) . $unit)
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
