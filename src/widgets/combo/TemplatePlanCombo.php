<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets\combo;

use hiqdev\combo\Combo;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * Class TemplatePlanCombo.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TemplatePlanCombo extends Combo
{
    /** {@inheritdoc} */
    public $type = 'plan/template';

    /** {@inheritdoc} */
    public $name = 'name';

    /** {@inheritdoc} */
    public $url = '/finance/plan/templates';

    /** {@inheritdoc} */
    public $_return = ['id'];

    /** {@inheritdoc} */
    public $_rename = ['text' => 'name'];

    public $_primaryFilter = 'name_ilike';

    /**
     * @var int ID of plan that will be used in this templates suggestion
     */
    public $plan_id;
    /**
     * @var string name of combo that contains target object
     */
    public $object_input_type;

    public function init()
    {
        parent::init();

        if (empty($this->plan_id)) {
            throw new InvalidConfigException('Property "plan_id" must be set');
        }
        $this->_filter['plan_id'] = ['format' => $this->plan_id];

        if (!empty($this->object_input_type)) {
            $this->_filter['object_id'] = [
                'field' => $this->object_input_type,
                'format' => 'id',
            ];
        }
    }

    public function getPluginOptions($options = [])
    {
        return parent::getPluginOptions(array_filter([
            'activeWhen' => $this->object_input_type,
            'select2Options' => [
                'ajax' => [
                    'type' => 'get',
                ],
                'templateResult' => new JsExpression("function (data) {
                    if (data.loading) {
                      return data.text;
                    }

                    return data.name + '<br><small>' +  data.reason + '</small>';
                }"),
                'templateSelection' => new JsExpression("function (data) {
                    if (!data.id.length) {
                        return data.text;
                    }

                    return data.name + ' &mdash; <small>' +  data.reason + '</small>';
                }"),
                'escapeMarkup' => new JsExpression('function (markup) {
                    return markup; // Allows HTML
                }'),
            ],
        ]));
    }
}
