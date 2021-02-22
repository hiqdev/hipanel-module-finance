<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\server\widgets\combo\ConfigCombo;
use hipanel\modules\stock\widgets\combo\ModelCombo;
use hipanel\widgets\DynamicFormWidget;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var Plan $plan */
/** @var Model $model */
/** @var Model[] $models */
/** @var string $type */

$this->registerCss(<<<CSS
.extra-item {
    display: flex;
    align-items: center;
}

.extra-item > div {
    align-self: baseline;
}

.extra-item > div:first-child {
    width: 70%;
}

.extra-item > div:last-child {
    width: 30%;
    padding-left: 2em;
}
CSS
);

Yii::$app->view->registerJs(<<<JS
const firstEl = $('#extra-prices-form select:input');
const changer = select => {
    $(select).on('change', function () {
        const el = $(this).find('option:selected');
        el.parents('.extra-item').find('input[id$="name"]').val(el.text());
    });
}
changer(firstEl);
$(".df_wrapper").on("afterInsert", function(e, item) {
  changer($(item).find('select:input'));
});
$('#extra-prices-form').submit(function(event) {
    event.preventDefault();
    const form = $(this);
    if (form.data('status') === 'sent') {
      return;
    }
    form.data('status', 'sent');
    const modal = $('#extra-prices-modal');
    form.find('button[type="submit"]').button('loading');
    const selection = _selection_grabber();
    $.ajax({
       url: form.attr('action'),
       method: 'POST',
       dataType: 'html',
       timeout: 0,
       data: form.serialize() + '&' + $.param({selection: selection}),
       error: xhr => {
           if (xhr.status > 300 && xhr.status < 400) {
               return; // redirects should not be handled in any manner
           }
           form.parent().html(xhr.responseText);
           hipanel.notify.error(xhr.statusText ? xhr.statusText : "")
       },
       success: newForm => {
           if (newForm.length) {
               $("#prices-form").replaceWith(newForm);
               hipanel.notify.success('Extra prices has been added');
           }
       },
       complete: () => {
           modal.modal('hide');
       }
   });
});
JS
);
?>

<?php $form = ActiveForm::begin([
    'id' => 'extra-prices-form',
    'action' => Url::to(['@price/add-extra-prices', 'plan_id' => $plan->id, 'type' => $type]),
    'enableClientValidation' => true,
]) ?>

<?php DynamicFormWidget::begin([
    'widgetContainer' => 'df_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-extra-items', // required: css class selector
    'widgetItem' => '.extra-item', // required: css class
    'limit' => 99, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-extra-item', // css class
    'deleteButton' => '.remove-extra-item', // css class
    'model' => $model,
    'formId' => 'extra-prices-form',
    'formFields' => [
        'id',
        'type',
        'name',
    ],
]) ?>

<div class="container-extra-items">
    <?php foreach ($models as $idx => $item) : ?>
        <div class="extra-item">
            <div>
                <?= Html::activeHiddenInput($item, "[{$idx}]type") ?>
                <?= Html::activeHiddenInput($item, "[{$idx}]name") ?>
                <?php if ($type === 'calculator_public_cloud') : ?>
                    <?= $form->field($item, "[{$idx}]id")->widget(ConfigCombo::class, [
                        'hasId' => true,
                        'pluginOptions' => [
                            'select2Options' => [
                                'placeholder' => Yii::t('hipanel:finance', 'Select config'),
                            ],
                        ],
                    ])->label(false) ?>
                <?php elseif ($type === 'calculator_private_cloud') : ?>
                    <?= $form->field($item, "[{$idx}]id")->widget(ModelCombo::class, [
                        'hasId' => true,
                        'pluginOptions' => [
                            'select2Options' => [
                                'placeholder' => Yii::t('hipanel:finance', 'Select model'),
                            ],
                        ],
                    ])->label(false) ?>
                <?php endif ?>
            </div>
            <div>
                <button type="button" class="add-extra-item btn btn-success"><i class="glyphicon glyphicon-plus"></i>
                </button>
                <button type="button" class="remove-extra-item btn btn-danger"><i class="glyphicon glyphicon-minus"></i>
                </button>
            </div>
        </div>
    <?php endforeach ?>
</div>

<?php DynamicFormWidget::end() ?>

<?= Html::submitButton(Yii::t('hipanel:finance', 'Add prices'), ['class' => 'btn btn-success', 'data-loading-text' => Yii::t('hipanel:finance', 'Adding extra prices...')]) ?>

<?php ActiveForm::end() ?>
