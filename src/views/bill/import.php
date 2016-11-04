<?php

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\forms\BillImportForm $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('hipanel/finance', 'Import payments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel/finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="bill-import">
    <?php $form = ActiveForm::begin([]) ?>
    <div class="container-items"><!-- widgetContainer -->
        <div class="row">
            <div class="col-md-4">
                <div class="box box-danger">
                    <div class="box-body">
                        <div class="form-instance">
                            <?php
                            print $form->field($model, 'data')->textarea();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
    <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>

    <?php ActiveForm::end(); ?>

</div>
