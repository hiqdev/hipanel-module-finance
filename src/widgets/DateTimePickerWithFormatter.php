<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use DateTime;
use hipanel\widgets\DateTimePicker;
use Yii;

class DateTimePickerWithFormatter extends DateTimePicker
{
    public function init(): void
    {
        $this->options = array_merge([
            'placeholder' => 'Select date and time',
            'value' => $this->resolveTimeValue($this->model),
        ], $this->options);
        parent::init();
    }

    private function resolveTimeValue($model): ?string
    {
        $formatter = Yii::$app->formatter;
        if (!isset($model->time)) {
            return $formatter->asDatetime(new DateTime(), 'php:Y-m-d H:i:s');
        }

        return $model->time !== false ? $formatter->asDatetime($model->time, 'php:Y-m-d H:i:s') : null;
    }
}
