<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use DateTime;
use hipanel\assets\BootstrapDatetimepickerAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

final class ConsumptionRepresentationMonthPicker extends Widget
{
    public function run(): string
    {
        BootstrapDatetimepickerAsset::register($this->view);
        $locale = Yii::$app->language;
        $formatter = Yii::$app->formatter;
        $this->view->registerJs(/* @lang JavaScript */ <<<"JS"
            const dateInput = $("input[name=month-picker]");
            dateInput.datetimepicker({
              minDate: moment().subtract(1, "year"),
              maxDate: moment(),
              locale: "$locale",
              viewMode: "months",
              format: "MMMM YYYY",
            });
            dateInput.datetimepicker().on("dp.update", function (evt) {
              $(".consumption-cell").each(function (index) {
                const el = $("span", this);
                if (el.length) {
                  const date = evt.viewDate.format("YYYY-MM");
                  const resources = el.data("resources");
                  if (resources.hasOwnProperty(date)) {
                    const { amount, unit } = resources[date];
                    el.text([amount, unit].join(" "));
                  } else {
                    el.text("");
                  }
                }
              });
            });
JS
        );
        $date = $formatter->asDate(new DateTime('now'), "MMMM YYYY");
        $input = Html::tag('div',
            implode('', [
                Html::textInput('month-picker',
                    '',
                    ['class' => 'form-control', 'id' => $this->getId(), 'placeholder' => $date]),
                Html::tag('span', null, ['class' => 'glyphicon glyphicon-calendar form-control-feedback text-muted']),
            ]),
            ['class' => 'form-group has-feedback']);

        return Html::tag('div', $input, ['class' => 'form-inline', 'style' => ['display' => 'inline-block']]);
    }
}
