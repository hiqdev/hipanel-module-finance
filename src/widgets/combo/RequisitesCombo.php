<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\combo;

use hiqdev\combo\Combo;
use yii\web\JsExpression;
use Yii;

class RequisitesCombo extends Combo
{
    /** {@inheritdoc} */
    public $type = 'finance/requisite';

    /** {@inheritdoc} */
    public $name = 'name';

    /** {@inheritdoc} */
    public $url = '/finance/requisite/search';

    /** {@inheritdoc} */
    public $_return = ['id', 'name', 'email', 'organization'];

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

                    var name = $('<div>').text(data.name).html();
                    var organization = $('<div>').text(data.organization).html();
                    return '<b>' + name + '</b><br>' + organization;
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
        if (Yii::$app->user->can('owner-staff')) {
            return parent::getFilter();
        }
        return \hipanel\helpers\ArrayHelper::merge(parent::getFilter(), [
            'client' => 'client/client',
        ]);
    }
}
