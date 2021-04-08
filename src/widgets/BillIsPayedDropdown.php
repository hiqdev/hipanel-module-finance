<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\BillSearch;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;

class BillIsPayedDropdown extends Widget
{
    public BillSearch $model;

    public string $attribute = 'is_payed';

    public function run(): string
    {
        return Html::activeDropDownList($this->model, $this->attribute, [
            0 => Yii::t('hipanel:finance', 'Not paid'),
            1 => Yii::t('hipanel:finance', 'Paid'),
        ], ['class' => 'form-control', 'prompt' => Yii::t('hipanel:finance', 'Payment status')]);
    }
}
