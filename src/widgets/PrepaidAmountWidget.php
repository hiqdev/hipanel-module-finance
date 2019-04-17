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

use hipanel\inputs\OptionsInput;
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

        if ($type instanceof OptionsInput) {
            echo $this->renderDropdownInput($type);
        } else {
            echo $this->renderTextInput($type);
        }
    }

    private function renderTextInput($type)
    {
        $this->activeField->template = "{label}\n<div class=\"input-group\">{input}</div>\n{hint}\n{error}";

        return $this->activeField->input('number', [
            'class' => 'form-control price-input',
            'autocomplete' => false,
            'step' => 'any',
            'data' => [
                'min-price' => $this->resource->getMinimumQuantity(),
            ],
            'value' => $this->resource->decorator()->getPrepaidQuantity(),
        ]);
    }

    private function renderDropdownInput(OptionsInput $boolean)
    {
        return $this->activeField->dropDownList($boolean->getOptions(), [
            'class' => 'form-control',
            'value' => $this->resource->decorator()->getPrepaidQuantity(),
        ]);
    }
}
