<?php

namespace hipanel\modules\finance\grid\presenters\price;

use hipanel\modules\finance\models\Price;
use hipanel\widgets\ArraySpoiler;
use Yii;
use yii\bootstrap\Html;

/**
 * Class PricePresenter contains methods that present price properties.
 * You can override this class to add custom presentations support.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PricePresenter
{
    /**
     * @var \yii\i18n\Formatter
     */
    private $formatter;

    public function __construct()
    {
        $this->formatter = Yii::$app->formatter;
    }

    /**
     * @param Price $price
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderPrice($price): string
    {
        $unit = $formula = '';
        if ($price->getUnitLabel()) {
            $unit = ' ' . Yii::t('hipanel:finance', 'per {unit}', ['unit' => $price->getUnitLabel()]);
        }

        if ($price->isOveruse()) {
            $prepaid = ', ' . Yii::t('hipanel:finance', 'prepaid {amount,number}', ['amount' => $price->quantity]);
        } else {
            $prepaid = ' ' . Yii::t('hipanel:finance', 'monthly');
        }

        if (count($price->formulaLines()) > 0) {
            $formula = ArraySpoiler::widget([
                'data' => $price->formulaLines(),
                'formatter' => function ($v) {
                    return Html::tag('kbd', $v, ['class' => 'javascript']);
                },
                'visibleCount' => 0,
                'delimiter' => '<br />',
                'button' => [
                    'label' => '&sum;',
                    'popoverOptions' => [
                        'placement' => 'bottom',
                        'html' => true,
                    ],
                ],
            ]);
        }

        return Html::tag('strong', $this->formatter->asCurrency($price->price, $price->currency)) . $unit . $prepaid . $formula;
    }
}
