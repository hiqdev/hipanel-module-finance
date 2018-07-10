<?php

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;

class AvailableMerchants extends Widget
{
    public $merchants = null;
    const PAYPAL_PROCESSING = 'PayPal';

    public function run()
    {
        if ($this->merchants === null && Yii::$app->hasModule('merchant')) {
            $this->merchants = Yii::$app->getModule('merchant')->getPurchaseRequestCollection()->getItems();
        }

        if (empty($this->merchants)) {
            return '';
        }

        $list = array_map(function($merchant) { return $merchant->label; }, $this->merchants);
        $_l = array_map(function($merchant) { return Html::tag('b', $merchant); }, $list);
        $_l = array_unique($_l);

        $out[] = Yii::t('hipanel:finance', 'We accept the following automatic payment methods') . ":";
        $out[] = implode(',&nbsp; ', $_l);
        if (in_array(self::PAYPAL_PROCESSING, $list, true)) {
            $out[] = Yii::t('hipanel:finance', 'as well as PayPal payments from your Visa and MasterCard');
        }

        return implode(" ", $out);
    }
}
