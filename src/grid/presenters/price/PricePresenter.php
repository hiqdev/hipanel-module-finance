<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

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

    /**
     * @var string
     */
    private $priceAttribute = 'price';

    public function __construct()
    {
        $this->formatter = Yii::$app->formatter;
    }

    public function setPriceAttribute(string $priceAttribute): self
    {
        $this->priceAttribute = $priceAttribute;

        return $this;
    }

    /**
     * @param Price $price
     * @throws \yii\base\InvalidConfigException
     * @return string
     */
    public function renderPrice(Price $price): string
    {
        $unit = $formula = '';
        if ($price->getUnitLabel()) {
            $unit = ' ' . Yii::t('hipanel:finance', 'per {unit}', ['unit' => $price->getUnitLabel()]);
        }

        if (\count($price->formulaLines()) > 0) {
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

        return Html::tag('strong', $this->formatter->asCurrency($price->{$this->priceAttribute}, $price->currency)) . $unit . $formula;
    }

    /**
     * @param Price $price
     * @return string
     */
    public function renderInfo(Price $price): string
    {
        if (!$price->isQuantityPredefined()) {
            return Yii::t('hipanel:finance', '{icon} Quantity: {quantity}', [
                'icon' => Html::tag('i', '', ['class' => 'fa fa-calculator']),
                'quantity' => Html::tag('b', '<i class="fa fa-spin fa-refresh"></i>', ['data-dynamic-quantity' => true]),
            ]);
        }
        if ($price->isOveruse()) {
            return Yii::t('hipanel:finance', '{coins}&nbsp;&nbsp;{amount,number} {unit}', [
                'coins' => Html::tag('i', '', ['class' => 'fa fa-money', 'title' => Yii::t('hipanel.finance.price', 'Prepaid amount')]),
                'amount' => $price->quantity,
                'unit' => $price->getUnitLabel(),
            ]);
        }

        if ($price->getSubtype() === 'hardware') {
            return $price->object->label;
        }

        return ''; // Do not display any information unless we are sure what we are displaying
    }
}
