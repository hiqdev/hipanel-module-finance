<?php

use hipanel\modules\finance\models\Plan;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var Plan $model
 * @var array $parentData
 */

?>

<?php $form = ActiveForm::begin([
    'options' => [
        'id' => 'link-parent-plan-form',
    ],
]) ?>

<div class="panel-footer">
    <div class="row">
        <div class="col-md-12">
            <?= Html::activeHiddenInput($model, 'id') ?>
            <?= $form->field($model, 'id')->dropDownList($parentData, ['prompt' => '--'])->label('Parent plan')  ?>
        </div>
    </div>
</div>
<?= Html::submitButton(Yii::t('hipanel', 'Link'), ['class' => 'btn btn-success']) ?> &nbsp;
<?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>

<?php $form::end() ?>
