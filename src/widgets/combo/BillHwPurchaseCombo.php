<?php

namespace hipanel\modules\finance\widgets\combo;

use hiqdev\combo\Combo;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class BillHwPurchaseCombo extends Combo
{
    /** {@inheritdoc} */
    public $type = 'bill/descr';

    /** {@inheritdoc} */
    public $name = 'descr';

    /** {@inheritdoc} */
    public $url = '/finance/bill/index';

    /** {@inheritdoc} */
    public $_return = ['id', 'client', 'sum', 'currency', 'descr', 'label', 'time'];

    /** {@inheritdoc} */
    public $_rename = ['text' => 'label'];

    /** {@inheritdoc} */
    public $_primaryFilter = 'descr';

    /** {@inheritdoc} */
    public function getPluginOptions($options = [])
    {
        return parent::getPluginOptions([
            'select2Options' => [
                'templateResult' => new JsExpression("function (data) {
                    if (data.loading) {
                        return data.text;
                    }
                    var client = '<b>' + data.client + ':&nbsp;'
                        color = data.sum < 0 ? 'text-danger' : 'text-success';
                        sum = ' <span class=\"' + color +'\">' + data.sum + '</span> '
                        currency = ' ' + data.currency.toUpperCase() + '</b><br>'
                        descr = (data.descr ? data.descr : data.label);

                    return client + sum + currency + (descr ? descr : '<span class=\"text-muted\">--</span>');
                }"),
                'escapeMarkup' => new JsExpression('function (markup) {
                    return markup; // Allows HTML
                }'),
            ],
        ]);
    }

    /** {@inheritdoc} */
    public function getFilter()
    {
        return ArrayHelper::merge(parent::getFilter(), [
            'type' => ['format' => 'other,hw_purchase'],
            'limit' => ['format' => '50'],
        ]);
    }
}
