<?php

use hipanel\modules\finance\models\Costprice;
use hipanel\widgets\MonthPicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Costprice $model
 * @var array $statistic
 * @var View $this
 */

$this->title = Yii::t('hipanel:finance', 'Costprice excel reports');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-success costprice-box">
            <div class="box-header with-border">
                <?php $form = ActiveForm::begin(); ?>
              <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'type')->dropDownList(Costprice::getAvailableReports())->label(false) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'month')->widget(MonthPicker::class)->label(false) ?>
                    </div>
                    <div class="col-md-6">
                        <div class="box-tools pull-right">
                            <div class="loading btn-box-tool" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i> <?= Yii::t('hipanel', 'loading...') ?>
                            </div>
                            <?= Html::submitButton(Yii::t('hipanel:finance', 'Load report'), ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
            </div>
            <div class="overlay" style="display: none;"></div>
        </div>
    </div>
</div>
