<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\models\Pnl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use hipanel\widgets\DynamicFormWidget;

/**
 * @var $models Pnl[]
 */

$this->registerCss(<<<CSS
.hint-block {
    padding-left: .5em;
    color: #999;
}

CSS
);

$this->registerJs(<<<JS
(() => {
  function bind(source, idContains) {
    source.addEventListener("keyup", (e) => {
      [].forEach.call(document.querySelectorAll(".container-items .form-control"), input => {
        if (input.matches("[id*='" + idContains + "']")) {
          input.value = e.target.value;
        }
      });
    });
  }
  bind(document.getElementById("global-note"), "note");
})();
JS
);

?>

<?php $form = ActiveForm::begin([
    'id' => 'set-pnl-notes-form',
    'action' => Url::to(['/finance/pnl/set-note']),
    'enableClientValidation' => true,
]) ?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item', // required: css class
    'limit' => 99, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => reset($models),
    'formId' => 'dynamic-form',
    'formFields' => [
        'part',
    ],
]) ?>

<div class="row container-items">
    <div class="col-md-12">
        <div class="form-group">
            <textarea rows="3" type="text" class="form-control" id="global-note" placeholder="Set global note"></textarea>
        </div>
    </div>
    <?php foreach ($models as $idx => $model) : ?>
        <div class="col-md-6 item">
            <?= $form->field($model, "[$idx]id")->hiddenInput(['vlaue' => $model->id])->label(false) ?>
            <?= $form->field($model, "[$idx]note")->textInput(['placeholder' => 'Note'])->label($model->charge_id)->hint($model->describePnl) ?>
        </div>
    <?php endforeach ?>
</div>

<div class="row">
    <div class="col-md-12 no">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>

<?php DynamicFormWidget::end() ?>

<?php ActiveForm::end() ?>
