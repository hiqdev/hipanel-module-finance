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

use hipanel\helpers\StringHelper;
use hipanel\modules\finance\models\Price;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Class TemplatePricePresenter.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TemplatePricePresenter extends PricePresenter
{
    /**
     * @param \hipanel\modules\finance\models\TemplatePrice $price
     * @return string
     */
    public function renderPrice(Price $price): string
    {
        $formatter = Yii::$app->formatter;

        $unit = '';
        if ($price->getUnitLabel()) {
            $unit = ' ' . Yii::t('hipanel:finance', 'per {unit}', ['unit' => $price->getUnitLabel()]);
        }

        if (StringHelper::startsWith($price->type, 'referral')) {
            $result = [
                Html::tag('strong', $price->rate . '%'),
            ];
        } else {
            $result = [
                Html::tag('strong', $formatter->asCurrency($price->price, $price->currency) . $unit),
            ];
        }
        if ($price->subprices) {
            foreach ($price->subprices as $currencyCode => $amount) {
                try {
                    $result[] = $formatter->asCurrency($amount, $currencyCode);
                } catch (InvalidConfigException $e) {
                    $result[] = $amount . ' ' . $currencyCode;
                }
            }
        }

        return implode('&nbsp;&mdash;&nbsp;', $result);
    }
}
