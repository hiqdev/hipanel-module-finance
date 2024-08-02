<?php
/**
 * @var Costprice $model
 * @var array $statistic
 * @var \yii\web\View $this
 */

use hipanel\modules\finance\models\Costprice;
use hipanel\widgets\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::t('hipanel:finance', 'Calculate costprice');
$this->params['breadcrumbs'][] = $this->title;
$generateUrl = Url::to(['@purse/generate-excel']);
$this->registerJs(/* @lang JavaScript */ <<<JS
        (() => {
          const loading = $(".costprice-box .loading");
          const runProgress = () => {
            hipanel.progress("$generateUrl").onMessage((event) => {
              $(".costprice-box .box-body").html(event.data);
            });
          }
          function sendGenerate(e) {
            e.preventDefault();
            if (loading.is(":visible")) {
              return;
            }
            
            let value = $("#costprice-type").val();
            value = value.replace('_excel', '').toLowerCase();
            hipanel.runProcess(
              "$generateUrl",
              { type: $("#costprice-type").val(), month: $("#costprice-month").val() },
              () => {
                loading.css("display", "inline-block");
              },
              () => {
                loading.hide();
                runProgress();
                hipanel.notify.success(`Generate request has been sent`);
              }
            );
          }
          $(".costprice-box form").on("submit", sendGenerate);
        })();
JS
    ,
    View::POS_END);

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
                                <?= Yii::t('hipanel:finance', 'Load report') ?>
                            </button>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
            </div>
            <div class="overlay" style="display: none;"></div>
        </div>
    </div>
</div>
