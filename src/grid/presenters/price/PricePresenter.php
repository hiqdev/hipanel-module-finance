<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid\presenters\price;

use hipanel\modules\finance\models\RepresentablePrice;
use hipanel\widgets\ArraySpoiler;
use NumberFormatter;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;
use yii\di\NotInstantiableException;
use yii\i18n\Formatter;

/**
 * Class PricePresenter contains methods that present price properties.
 * You can override this class to add custom presentations support.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PricePresenter
{
    protected string $priceAttribute = 'price';

    public function __construct(
        readonly protected Formatter $formatter,
        readonly protected bool $canReadPrices,
    )
    {
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
     * @param RepresentablePrice $price
     * @return string
     * @throws InvalidConfigException
     * @throws Throwable
     * @throws NotInstantiableException
     */
    public function renderPrice(RepresentablePrice $price): string
    {
        $unit = $formula = '';
        if ($price->getUnitLabel()) {
            $unit = ' ' . Yii::t('hipanel:finance', 'per {unit}', ['unit' => Html::encode($price->getUnitLabel())]);
        }

        $activeFormulas = array_filter($price->getFormulaLines(), fn($el) => $el['is_actual']);
        if (!empty($activeFormulas)) {
            $formula = ArraySpoiler::widget([
                'id' => uniqid('f_'),
                'data' => $activeFormulas,
                'formatter' => fn($v) => Html::tag('kbd', Html::encode($v['formula']), ['class' => 'javascript']),
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
        $sum = $this->formatter->asCurrency(
            $price->{$this->priceAttribute},
            $price->currency,
            [NumberFormatter::MAX_FRACTION_DIGITS => 20]
        );

        return Html::tag('strong', $sum) . $unit . $formula;
    }

    /**
     * @param RepresentablePrice $price
     * @param string $attribute
     * @return string
     */
    public function renderInfo(RepresentablePrice $price, string $attribute = 'quantity'): string
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

        if ($price->getSubtype() === 'hardware' && $this->canReadPrices) {
            return $price->object->label ?? $price->object->name;
        }

        return ''; // Do not display any information unless we are sure what we are displaying
    }
}
