<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\assets\BootstrapDatetimepickerAsset;
use Yii;
use yii\base\Widget;
use yii\db\ActiveRecordInterface;
use yii\helpers\Html;

final class MonthRangePicker extends Widget
{
    public ActiveRecordInterface $model;

    public string $timeFromAttribute = 'time_from';

    public string $timeTillAttribute = 'time_till';

    public function run(): string
    {
        BootstrapDatetimepickerAsset::register($this->view);
        $locale = Yii::$app->language;
        $this->view->registerJs(/* @lang JavaScript */ <<<"JS"
            const dateInput = $('input[name=month-picker]');
            const getDateFromInput = (dateIntput) => {
              const fromInputDate = dateIntput.closest('form').find('input[name*=$this->timeFromAttribute]').val();

              return moment(fromInputDate);
            };
            dateInput.datetimepicker({
              date: getDateFromInput(dateInput),
              maxDate: moment(),
              locale: '$locale',
              viewMode: 'months',
              format: 'MMMM YYYY'
            });
            dateInput.datetimepicker().on('dp.update', evt => {
              const date = evt.viewDate;
              const form = $(evt.target).closest('form');
              form.find('input[name*=$this->timeFromAttribute]').val(date.startOf('month').format('YYYY-MM-DD'));
              form.find('input[name*=$this->timeTillAttribute]').val(date.add(1, 'month').startOf('month').format('YYYY-MM-DD'));
            });
JS
        );

        return Html::tag('div', implode('', [
            Html::textInput('month-picker', '', ['class' => 'form-control', 'id' => $this->getId()]),
            Html::tag('span', null, ['class' => 'glyphicon glyphicon-calendar form-control-feedback text-muted']),
            Html::activeHiddenInput($this->model, $this->timeFromAttribute),
            Html::activeHiddenInput($this->model, $this->timeTillAttribute),
        ]), ['class' => 'form-group has-feedback']);
    }
}
