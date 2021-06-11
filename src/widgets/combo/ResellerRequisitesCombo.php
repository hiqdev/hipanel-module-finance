<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\combo;

use hiqdev\combo\Combo;
use yii\web\JsExpression;

class ResellerRequisitesCombo extends Combo
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

                    var name = data.name.replace(/(<([^>]+)>)/ig,'');
                    var organization = data.organization.replace(/(<([^>]+)>)/ig,'');
                    return '<b>' + name + '</b><br>' + organization;
                }"),
                'escapeMarkup' => new JsExpression('function (markup) {
                    return markup; // Allows HTML
                }'),
            ],
            'clearWhen' => ['client/seller'],
            'affects'   => [
                'client/seller' => 'client',
            ],
        ]);
    }

    /** {@inheritdoc} */
    public function getFilter()
    {
        return \hipanel\helpers\ArrayHelper::merge(parent::getFilter(), [
            'client' => 'client/seller',
        ]);
    }
}
