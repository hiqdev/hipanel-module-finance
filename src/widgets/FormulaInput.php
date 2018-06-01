<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\Price;
use hiqdev\assets\autosize\AutosizeAsset;
use yii\bootstrap\Html;
use yii\helpers\BaseHtml;
use yii\widgets\InputWidget;


/**
 * Class FormulaInput
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FormulaInput extends InputWidget
{
    /** @var Price */
    public $model;

    private function getAttributeValue()
    {
        return BaseHtml::getAttributeName($this->attribute);
    }

    public function registerClientScript()
    {
        AutosizeAsset::register($this->view);
        $this->view->registerJs("autosize($('.formula-input'));");
    }

    public function run()
    {
        $this->registerClientScript();

        return Html::activeTextarea($this->model, $this->attribute, [
            'class' => 'form-control formula-input',
            'rows' => $this->formulaLinesCount(),
        ]);
    }

    private function formulaLinesCount()
    {
        return count(explode("\n", $this->getAttributeValue()));
    }
}
