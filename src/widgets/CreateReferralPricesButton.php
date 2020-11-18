<?php

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;

class CreateReferralPricesButton extends CreatePricesButton
{
    public function run()
    {
        return Html::tag('div', Html::a(Yii::t('hipanel.finance.price', 'Create prices') .
                '&nbsp;&nbsp;' .
                Html::tag('span', null, ['class' => 'caret']), '#', [
                'data-toggle' => 'dropdown', 'class' => 'dropdown-toggle btn btn-success btn-sm',
            ]) . Dropdown::widget([
                'options' => ['class' => 'pull-right'],
                'items' => array_filter([
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
                ]),
            ]), ['class' => 'dropdown']);
    }
}
