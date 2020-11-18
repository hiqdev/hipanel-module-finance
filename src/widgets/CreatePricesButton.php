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
use yii\web\View;

class CreatePricesButton extends Widget
{
    /**
     * @var Plan
     */
    public $model;

    public function init()
    {
        $this->view->on(View::EVENT_END_BODY, function () {
            echo AjaxModal::widget([
                'id' => 'create-prices-modal',
                'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create prices'), ['class' => 'modal-title']),
                'scenario' => 'create-prices',
                'actionUrl' => ['@plan/suggest-prices-modal', 'id' => $this->model->id],
                'size' => Modal::SIZE_SMALL,
                'toggleButton' => false,
            ]);
            if ($this->model->supportsSharedPricesCreation()) {
                echo AjaxModal::widget([
                    'id' => 'create-shared-prices-modal',
                    'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create shared prices'),
                        ['class' => 'modal-title']),
                    'scenario' => 'create-prices',
                    'actionUrl' => ['@plan/suggest-shared-prices-modal', 'id' => $this->model->id],
                    'size' => Modal::SIZE_SMALL,
                    'toggleButton' => false,
                ]);
            }
            if ($this->model->is_grouping) {
                echo AjaxModal::widget([
                    'id' => 'create-grouping-prices-modal',
                    'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create grouping prices'), ['class' => 'modal-title']),
                    'scenario' => 'create-prices',
                    'actionUrl' => ['@plan/suggest-grouping-prices-modal', 'id' => $this->model->id],
                    'size' => Modal::SIZE_SMALL,
                    'toggleButton' => false,
                ]);
            }
        });
    }

    public function run()
    {
        return Html::tag('div', Html::a(Yii::t('hipanel.finance.price', 'Create prices') .
                '&nbsp;&nbsp;' .
                Html::tag('span', null, ['class' => 'caret']), '#', [
                'data-toggle' => 'dropdown', 'class' => 'dropdown-toggle btn btn-success btn-sm',
            ]) . Dropdown::widget([
                'options' => ['class' => 'pull-right'],
                'items' => array_filter([
                    $this->model->supportsIndividualPricesCreation() ? [
                        'encode' => false,
                        'url' => '#',
                        'label' => Yii::t('hipanel.finance.price', 'Create prices'),
                        'linkOptions' => [
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#create-prices-modal',
                            ],
                        ],
                    ] : null,
                    $this->model->supportsSharedPricesCreation() ? [
                        'encode' => false,
                        'url' => '#',
                        'label' => Yii::t('hipanel.finance.price', 'Create shared prices'),
                        'linkOptions' => [
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#create-shared-prices-modal',
                            ],
                        ],
                    ] : null,
                    $this->model->is_grouping ? [
                        'encode' => false,
                        'url' => '#',
                        'label' => Yii::t('hipanel.finance.price', 'Create grouping prices'),
                        'linkOptions' => [
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#create-grouping-prices-modal',
                            ],
                        ],
                    ] : null,
                ]),
            ]), ['class' => 'dropdown']);
    }
}
