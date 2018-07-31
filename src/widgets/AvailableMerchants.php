<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\Merchant;
use yii\base\Widget;
use yii\helpers\Html;
use Yii;

class AvailableMerchants extends Widget
{
    /**
     * @var Merchant[]
     */
    public $merchants;

    const PAYPAL_PROCESSING = 'PayPal';

    public function run()
    {
        if ($this->merchants === null && Yii::$app->hasModule('merchant') && Yii::$app->user->can('deposit')) {
            $this->merchants = Yii::$app->getModule('merchant')->getPurchaseRequestCollection()->getItems();
        }

        if (empty($this->merchants)) {
            return '';
        }

        $list = array_map(function($merchant) { return $merchant->label; }, $this->merchants);
        $htmlList = array_unique(array_map(function($merchant) { return Html::tag('b', $merchant); }, $list));

        $out[] = Yii::t('hipanel:finance', 'We accept the following automatic payment methods') . ":";
        $out[] = implode(',&nbsp; ', $htmlList);
        if (\in_array(self::PAYPAL_PROCESSING, $list, true)) {
            $out[] = Yii::t('hipanel:finance', 'as well as PayPal payments from your Visa and MasterCard');
        }

        return implode(" ", $out);
    }
}
