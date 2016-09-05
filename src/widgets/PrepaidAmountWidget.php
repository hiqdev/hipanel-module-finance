<?php

namespace hipanel\modules\finance\widgets;

use hipanel\inputs\BooleanInput;
use hipanel\modules\finance\models\ServerResource;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveField;

final class PrepaidAmountWidget extends Widget
{
    public $model;
    public $attribute;

    /**
     * @var ActiveField
     */
    public $activeField;

    /** @var ServerResource */
    public $resource;

    public function init()
    {
        Html::addCssClass($this->activeField->options, 'form-group-sm');
    }

    public function run()
    {
        $type = $this->resource->decorator()->prepaidAmountType();

        if ($type instanceof BooleanInput) {
            echo $this->renderDropdownInput($type);
        } else {
            echo $this->renderTextInput($type);
        }
    }

    private function renderTextInput($type)
    {
        $this->activeField->template = "{label}\n<div class=\"input-group\">{input}<span class=\"input-group-addon\">{unit}</span></div>\n{hint}\n{error}";
        $this->activeField->parts = ['{unit}' => $this->resource->decorator()->displayUnit()];

        return $this->activeField->input('number', [
            'class' => 'form-control price-input',
            'autocomplete' => false,
            'step' => 'any',
            'value' => $this->resource->decorator()->getPrepaidQuantity(),
        ]);
    }

    private function renderDropdownInput(BooleanInput $boolean)
    {
        return $this->activeField->dropDownList($boolean->getOptions(), [
            'class' => 'form-control',
            'value' => $this->resource->decorator()->getPrepaidQuantity(),
        ]);
    }
}
