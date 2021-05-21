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
use yii\i18n\Formatter;
use yii\web\User;

/**
 * Class PricePresenter contains methods that present price properties.
 * You can override this class to add custom presentations support.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PricePresenter
{
    protected Formatter $formatter;

    protected User $user;

    protected string $priceAttribute = 'price';

    public function __construct(Formatter $formatter, User $user)
    {
        $this->formatter = $formatter;
        $this->user = $user;
    }

    /**
     * @param string $priceAttribute
     * @return $this
     */
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

        $activeFormulas = array_filter($price->getFormulaLines(), fn ($el) => $el['is_actual']);
        if (!empty($activeFormulas)) {
            $formula = ArraySpoiler::widget([
                'data' => $activeFormulas,
                'formatter' => function ($v) {
                    return Html::tag('kbd', Html::encode($v['formula']), ['class' => 'javascript']);
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
     * @param string $attribute
     * @return string
     */
    public function renderInfo(Price $price, string $attribute = 'quantity'): string
    {
        if (!$price->isQuantityPredefined()) {
            return Yii::t('hipanel:finance', '{icon} Quantity: {quantity}', [
                'icon' => Html::tag('i', '', ['class' => 'fa fa-calculator']),
                'quantity' => Html::tag('b', '<i class="fa fa-spin fa-refresh"></i>', ['data-dynamic-quantity' => true]),
            ]);
        }
        if ($price->isOveruse()) {
            return Yii::t('hipanel:finance', '{coins}&nbsp;&nbsp;{amount,number} {unit}{aggregated}', [
                'coins' => Html::tag('i', '', ['class' => 'fa fa-money', 'title' => Yii::t('hipanel.finance.price', 'Prepaid amount')]),
                'amount' => $price->{$attribute},
                'unit' => $price->getUnitLabel(),
                'aggregated' => $price->hasAttribute('count_aggregated_traffic') && $price->count_aggregated_traffic
                    ? Html::tag('span', Yii::t('hipanel.finance.price', 'Aggregated'), ['class' => 'label bg-olive pull-right'])
                    : '',
            ]);
        }

        if ($price->getSubtype() === 'hardware' && $this->user->can('part.read')) {
            return $price->object->label ?? $price->object->name;
        }

        return ''; // Do not display any information unless we are sure what we are displaying
    }
}
