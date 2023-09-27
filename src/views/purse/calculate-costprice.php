<?php
/**
 * @var Costprice $model
 * @var array $statistic
 * @var \yii\web\View $this
 */

use hipanel\modules\finance\models\Costprice;
use hipanel\modules\finance\widgets\ProcessTableGenerator;
use hipanel\widgets\DateTimePicker;
use hipanel\widgets\DynamicFormWidget;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('hipanel:finance', 'Calculate costprice');
$this->params['subtitle'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Finance tools'), 'url' => ['finance-tools']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item', // required: css class
    'limit' => 99, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $model,
    'formId' => 'costprice-form',
    'formFields' => [
        'type',
    ],
]) ?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-widget">
            <div class="box-header with-border">
                <?php $form = ActiveForm::begin(); ?>

                <div class="col-md-3">
                    <?= $form->field($model, 'type')->dropDownList(Costprice::getAvailableType()) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'month')->widget(DateTimePicker::class, [
                        'clientOptions' => [
                            'format' => 'yyyy-mm-01',
                            'minView' => 2,
                            'todayHighlight' => true,
                        ],
                    ]) ?>
                </div>
                <div class="box-tools pull-right">
                    <div class="loading btn-box-tool" style="display: none;">
                        <i class="fa fa-refresh fa-spin"></i> <?= Yii::t('hipanel', 'loading...') ?>
                    </div>
                    <button
                        type="submit"
                        onclick="sendRecalculate()"
                        class="btn btn-default btn-box-tool"
                    >
                        <?= Yii::t('hipanel:document', 'Calculate costprice') ?>
                    </button>
                </div>
                <?php ActiveForm::end() ?>
            </div>
            <div class="box-body no-padding">
                <?= ProcessTableGenerator::widget(['statistic' => $statistic]) ?>
            </div>
            <div class="overlay" style="display: none;">
            </div>
        </div>
    </div>
</div>
