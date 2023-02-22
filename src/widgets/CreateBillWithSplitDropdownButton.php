<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;

class CreateBillWithSplitDropdownButton extends Widget
{
    public array $dropdownItems = [];

    public function run(): string
    {
        $html = Html::beginTag('div', ['class' => 'btn-group']);
        $html .= Html::a(
            Yii::t('hipanel:finance', 'Add payment'),
            ['@bill/create'],
            ['class' => 'btn btn-sm btn-success']
        );
        if (Yii::$app->user->can('test.alpha')) {
            $html .= Html::button(
                Html::tag('span', null, ['class' => 'caret']) .
                Html::tag('span', Yii::t('hipanel', 'Toggle dropdown'), ['class' => 'sr-only']),
                ['class' => 'btn btn-success btn-sm dropdown-toggle', 'data-toggle' => 'dropdown']
            );
            $html .= Dropdown::widget([
                'items' => $this->dropdownItems === [] ? [
                    [
                        'label' => Yii::t('hipanel:finance', 'Expense template'),
                        'url'   => ['@finance/bill/create', 'template' => 'expense'],
                    ],
                ] : $this->dropdownItems,
            ]);
        }
        $html .= Html::endTag('div');

        return $html;
    }
}
