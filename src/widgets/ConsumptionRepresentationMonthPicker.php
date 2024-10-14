<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use DateTime;
use hipanel\assets\BootstrapDatetimepickerAsset;
use hipanel\modules\server\models\HubSearch;
use hipanel\modules\server\models\ServerSearch;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

final class ConsumptionRepresentationMonthPicker extends Widget
{
    public ServerSearch|HubSearch $model;
    public string $attribute = 'uses_month';

    public function run(): string
    {
        BootstrapDatetimepickerAsset::register($this->view);
        $locale = Yii::$app->language;
        $fmt = Yii::$app->formatter;
        $this->view->registerJs(/* @lang JavaScript */ <<<"JS"
            const dateInput = $("input[name*=uses_month]");
            dateInput.datetimepicker({
              minDate: moment().subtract(10, "year"),
              maxDate: moment(),
              locale: "$locale",
              viewMode: "months",
              format: "MMM YYYY",
              showClear: true,
            });
JS
        );
        $input = Html::tag('div',
            implode('', [
                Html::activeTextInput($this->model,
                    'uses_month',
                    [
                        'class' => 'form-control',
                        'id' => $this->getId(),
                        'placeholder' => Yii::t('hipanel:server', $this->model->getAttributeLabel($this->attribute), $fmt->asDate(new DateTime('now'), 'MMM YYYY')),
                    ]),
                Html::tag('span', null, ['class' => 'glyphicon glyphicon-calendar form-control-feedback text-muted']),
            ]),
            ['class' => 'form-group has-feedback', 'autocomplete' => 'off']);

        return Html::tag('div', $input);
    }
}
