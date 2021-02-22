<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\Plan;
use hipanel\widgets\AjaxModal;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Inflector;
use yii\web\View;

class CreateCalculatorPricesButton extends Widget
{
    public Plan $model;

    public array $tabs = [
        'public_cloud',
        'private_cloud',
        'storage',
    ];

    public function init(): void
    {
        $this->view->on(View::EVENT_END_BODY, function () {
            foreach ($this->tabs as $tab) {
                echo AjaxModal::widget([
                    'id' => "create-calculator-{$tab}-prices-modal",
                    'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create prices'), ['class' => 'modal-title']),
                    'scenario' => 'create-prices',
                    'actionUrl' => ['@plan/suggest-calculator-prices-modal', 'id' => $this->model->id, 'type' => 'calculator_' . $tab],
                    'size' => Modal::SIZE_SMALL,
                    'toggleButton' => false,
                ]);
            }
        });
    }

    public function run(): string
    {
        $items = [];
        foreach ($this->tabs as $tab) {
            $items[] = [
                'encode' => false,
                'url' => '#',
                'label' => Inflector::humanize($tab),
                'linkOptions' => [
                    'data' => [
                        'toggle' => 'modal',
                        'target' => "#create-calculator-{$tab}-prices-modal",
                    ],
                ],
            ];
        }

        return Html::tag('div', Html::a(Yii::t('hipanel.finance.price', 'Create prices') .
                '&nbsp;&nbsp;' .
                Html::tag('span', null, ['class' => 'caret']), '#', [
                'data-toggle' => 'dropdown', 'class' => 'dropdown-toggle btn btn-success btn-sm',
            ]) . Dropdown::widget([
                'options' => ['class' => 'pull-right'],
                'items' => $items,
            ]), ['class' => 'btn-group']);
    }
}
