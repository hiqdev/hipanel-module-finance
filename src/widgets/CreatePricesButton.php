<?php

namespace hipanel\modules\finance\widgets;

use hipanel\widgets\AjaxModal;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\web\View;

class CreatePricesButton extends Widget
{
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
            echo AjaxModal::widget([
                'id' => 'create-common-prices-modal',
                'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create common prices'), ['class' => 'modal-title']),
                'scenario' => 'create-prices',
                'actionUrl' => ['@plan/suggest-common-prices-modal', 'id' => $this->model->id],
                'size' => Modal::SIZE_SMALL,
                'toggleButton' => false,
            ]);
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
                'items' => [
                    [
                        'encode' => false,
                        'url' => '#',
                        'label' => Yii::t('hipanel.finance.price', 'Create prices'),
                        'linkOptions' => [
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#create-prices-modal',
                            ],
                        ],
                    ],
                    [
                        'encode' => false,
                        'url' => '#',
                        'label' => Yii::t('hipanel.finance.price', 'Create common prices'),
                        'linkOptions' => [
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#create-common-prices-modal',
                            ],
                        ],
                    ],
                ],
            ]), ['class' => 'dropdown']);
    }
}
