<?php

/** @var AdvancedSearch $search */

use hipanel\widgets\AdvancedSearch;
use hiqdev\yii2\daterangepicker\DateRangePicker;
use yii\web\JsExpression;

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('charge_ids') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('charge_id') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('month') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
        <?= DateRangePicker::widget([
            'model' => $search->model,
            'attribute' => 'month_from',
            'attribute2' => 'month_till',
            'options' => [
                'class' => 'form-control',
                'placeholder' => Yii::t('hipanel', 'Month range'),
            ],
            'dateFormat' => 'yyyy-MM-dd',
            'clientOptions' => [
                'showDropdowns' => true,
                'minDate' => new JsExpression("moment().year('2022')"),
                'maxDate' => new JsExpression("moment().add(1, 'year').endOf('year')"),
                'ranges' => [
                    Yii::t('hipanel', 'Current Month') => new JsExpression('[moment().startOf("month"), new Date()]'),
                    Yii::t('hipanel', 'Previous Month') => new JsExpression('[moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]'),

                    Yii::t('hipanel', 'Current year') => new JsExpression('[moment().startOf("year"), moment().endOf("year")]'),
                    Yii::t('hipanel', 'I quarter of this year') => new JsExpression('[moment().startOf("year"), moment().startOf("year").endOf("quarter")]'),
                    Yii::t('hipanel', 'II quarter of this year') => new JsExpression('[moment().startOf("year").add(1, "quarter"), moment().startOf("year").add(1, "quarter").endOf("quarter")]'),
                    Yii::t('hipanel', 'III quarter of this year') => new JsExpression('[moment().startOf("year").add(2, "quarter"), moment().startOf("year").add(2, "quarter").endOf("quarter")]'),
                    Yii::t('hipanel', 'IV quarter of this year') => new JsExpression('[moment().startOf("year").add(3, "quarter"), moment().startOf("year").add(3, "quarter").endOf("quarter")]'),
                    Yii::t('hipanel', 'First half of this year') => new JsExpression('[moment().startOf("year"), moment().startOf("year").add(2, "quarter")]'),
                    Yii::t('hipanel', 'Second half of this year') => new JsExpression('[moment().startOf("year").add(2, "quarter"), moment().endOf("year")]'),

                    Yii::t('hipanel', 'Previous year') => new JsExpression('[moment().subtract(1, "year").startOf("year"), moment().subtract(1, "year").endOf("year")]'),
                    Yii::t('hipanel', 'I quarter of previous year') => new JsExpression('[moment().subtract(1, "year").startOf("year"), moment().subtract(1, "year").endOf("quarter")]'),
                    Yii::t('hipanel', 'II quarter of previous year') => new JsExpression('[moment().subtract(1, "year").startOf("year").add(1, "quarter"), moment().subtract(1, "year").startOf("year").add(1, "quarter").endOf("quarter")]'),
                    Yii::t('hipanel', 'III quarter of previous year') => new JsExpression('[moment().subtract(1, "year").startOf("year").add(2, "quarter"), moment().subtract(1, "year").startOf("year").add(2, "quarter").endOf("quarter")]'),
                    Yii::t('hipanel', 'IV quarter of previous year') => new JsExpression('[moment().subtract(1, "year").startOf("year").add(3, "quarter"), moment().subtract(1, "year").startOf("year").add(3, "quarter").endOf("quarter")]'),
                    Yii::t('hipanel', 'First half of previous year') => new JsExpression('[moment().subtract(1, "year").startOf("year"), moment().subtract(1, "year").startOf("year").add(2, "quarter")]'),
                    Yii::t('hipanel', 'Second half of previous year') => new JsExpression('[moment().subtract(1, "year").startOf("year").add(2, "quarter"), moment().subtract(1, "year").endOf("year")]'),
                ],
            ],
        ]) ?>
    </div>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('has_no_type', ['options' => ['class' => 'form-group checkbox']])->checkbox(['class' => 'option-input']) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('has_note', ['options' => ['class' => 'form-group checkbox']])->checkbox(['class' => 'option-input']) ?>
</div>
