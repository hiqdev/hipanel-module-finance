<?php

namespace hipanel\modules\finance\validation;

use hipanel\modules\finance\forms\BillForm;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\validators\Validator;

class BillChargesSumValidator extends Validator
{
    public function init()
    {
        parent::init();

        $this->message = Yii::t('hipanel:finance', 'Bill sum must match charges sum');
    }

    /**
     * @param BillForm $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        if (count($model->charges) > 0) {
            $chargesSum = array_sum(ArrayHelper::getColumn($model->charges, 'sum'));
            if ($model->sum != -$chargesSum) {
                $model->addError($attribute, $this->message . ': ' . -$chargesSum);
            }
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = Json::encode($this->message);

        return <<<JS
        var sum = 0,
            inputs = $(this.input).closest('.bill-item').find('input[data-attribute="sum"]');

        if (inputs.length > 0) {
            inputs.map(function () {
                sum += Number($(this).val()) * 100;
            });
            if (isNaN(sum)) {
                return;
            }

            if (Math.round(value * 100) != - Math.round(sum)) {
                messages.push($message + ': ' + (-(sum / 100)));
            }
        }
JS;
    }
}
