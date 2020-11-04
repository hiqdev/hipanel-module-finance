<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\combo;

use hiqdev\combo\Combo;
use yii\web\JsExpression;

class RequisitesCombo extends Combo
{
    /** {@inheritdoc} */
    public $type = 'finance/requisite';

    /** {@inheritdoc} */
    public $name = 'name';

    /** {@inheritdoc} */
    public $url = '/finance/requisite/index';

    /** {@inheritdoc} */
    public $_return = ['id', 'name', 'email'];

    /** {@inheritdoc} */
    public $_rename = ['text' => 'name'];

    public $_primaryFilter = 'name_ilike';

    public function getPluginOptions($options = [])
    {
        return parent::getPluginOptions([
            'select2Options' => [
                'templateResult' => new JsExpression("function (data) {
                    if (data.loading) {
                        return data.text;
                    }

                    return data.name + '<br>' + data.email;
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
        return \hipanel\helpers\ArrayHelper::merge(parent::getFilter(), [
            'client' => 'client/client',
        ]);
    }
}
