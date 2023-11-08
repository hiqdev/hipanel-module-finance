<?php
/**
 * @var Costprice $model
 * @var array $statistic
 * @var \yii\web\View $this
 */

use hipanel\modules\finance\models\Costprice;
use hipanel\modules\finance\widgets\ProcessTableGenerator;
use hipanel\widgets\DateTimePicker;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('hipanel:finance', 'Calculate costprice');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-success costprice-box">
            <div class="box-header with-border">
                <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'type')->dropDownList(Costprice::getAvailableType())->label(false) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'month')->widget(DateTimePicker::class, [
                            'clientOptions' => [
                                'format' => 'yyyy-mm-01',
                                'minView' => 2,
                                'todayHighlight' => true,
                            ],
                        ])->label(false) ?>
                    </div>
                    <div class="col-md-6">
                        <div class="box-tools pull-right">
                            <div class="loading btn-box-tool" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i> <?= Yii::t('hipanel', 'loading...') ?>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">
                                <?= Yii::t('hipanel:finance', 'Calculate costprice') ?>
                            </button>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
            </div>
            <div class="box-body">
                <?= ProcessTableGenerator::widget(['statistic' => $statistic]) ?>
            </div>
            <div class="overlay" style="display: none;"></div>
        </div>
    </div>
</div>
