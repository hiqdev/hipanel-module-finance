<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\widgets\BillTypeVueTreeSelect;
use hipanel\modules\finance\widgets\DateTimePickerWithFormatter;
use hipanel\modules\finance\widgets\combo\BillRequisitesCombo;
use hipanel\modules\finance\widgets\SumSignToggleButton;
use hipanel\modules\finance\widgets\TreeSelectBehavior;
use hipanel\modules\finance\widgets\UpdateChargeTimeScript;
use hipanel\widgets\AmountWithCurrency;
use hipanel\widgets\DynamicFormWidget;
use hipanel\widgets\combo\ObjectCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var BillForm $model
 * @var BillForm[] $models
 * @var array $billTypesList
 * @var array $allowedTypes
 */


UpdateChargeTimeScript::widget(['model' => $model]);

$this->registerCss("
#bill-dynamic-form .charge-item .col-md-1,
#bill-dynamic-form .charge-item .col-md-2,
#bill-dynamic-form .charge-item .col-md-3,
#bill-dynamic-form .charge-item .col-md-4,
#bill-dynamic-form .charge-item .col-md-5,
#bill-dynamic-form .charge-item .col-md-6,
#bill-dynamic-form .charge-item .col-md-7,
#bill-dynamic-form .charge-item .col-md-8,
#bill-dynamic-form .charge-item .col-md-9,
#bill-dynamic-form .charge-item .col-md-10,
#bill-dynamic-form .charge-item .col-md-11,
#bill-dynamic-form .charge-item .col-md-12 {
  padding-right: 10px;
  padding-left: 10px;
}
#bill-dynamic-form .charge-item .row {
  margin-right: -10px;
  margin-left: -10px;
}

");

$form = ActiveForm::begin([
    'id' => 'bill-dynamic-form',
    'action' => $model->isNewRecord ? Url::to(['@bill/create']) : Url::to(['@bill/update', 'id' => $model->id]),
    'enableClientValidation' => true,
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute([
        'validate-bill-form',
        'scenario' => $model->scenario,
    ]),
]) ?>
<?php DynamicFormWidget::begin([
    'widgetContainer' => 'bills_dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.bill-item', // required: css class
    'limit' => 99, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $model,
    'formId' => 'bill-dynamic-form',
    'formFields' => [
        'class',
        'object_id',
        'client_id',
        'requisite_id',
        'type',
        'sum',
        'time',
        'label',
    ],
]) ?>

<div class="container-items">
    <?php $i = 0; ?>
    <?php foreach ($models as $model) : ?>
        <div class="bill-item">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <div class="box-title">
                        <?= SumSignToggleButton::widget() ?>
                    </div>
                    <div class="box-tools">
                        <?php if ($model->isNewRecord) : ?>
                            <div class="btn-group">
                                <button type="button" class="add-item btn btn-box-tool">
                                    <i class="glyphicon glyphicon-plus text-success"></i>
                                </button>
                                <button type="button" class="remove-item btn btn-box-tool">
                                    <i class="glyphicon glyphicon-minus text-danger"></i>
                                </button>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="box-body">

                    <div class="row input-row">
                        <?= Html::activeHiddenInput($model, "[$i]id") ?>
                        <div class="form-instance">
                            <div class="col-md-3">
                                <?= $form->field($model, "[$i]object_id")->widget(ObjectCombo::class, [
                                    'class_attribute_name' => "[$i]class",
                                ]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, "[$i]client_id")->widget(ClientCombo::class, [
                                    'formElementSelector' => '.form-instance',
                                    'inputOptions' => [
                                        'readonly' => $model->scenario === Bill::SCENARIO_UPDATE,
                                    ],
                                    'current' => [
                                        $model->client_id => $model->client
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, "[$i]type_id")->widget(BillTypeVueTreeSelect::class, [
                                    'billTypes' => $billTypesList,
                                    'replaceAttribute' => 'type_id',
                                    'deprecatedTypes' => Yii::$app->params['module.finance.bill.types']['deprecated.types'],
                                    'behavior' => $model->isNewRecord ? TreeSelectBehavior::Hidden : TreeSelectBehavior::Disabled,
                                    'allowedTypes' => $allowedTypes,
                                ]) ?>
                            </div>
                            <div class="col-md-2 <?= AmountWithCurrency::$widgetClass ?>">
                                <?= $form->field($model, "[$i]sum")->widget(AmountWithCurrency::class, [
                                    'currencyAttributeName' => "[$i]currency",
                                    'currencyAttributeOptions' => [
                                        'items' => $this->context->getCurrencyTypes(),
                                    ],
                                    'options' => [
                                        'data-bill-sum' => true,
                                    ],
                                ]) ?>
                                <?= $form->field($model, "[$i]currency", ['template' => '{input}{error}'])->hiddenInput() ?>
                            </div>
                            <div class="col-md-1">
                                <?= Html::activeHiddenInput($model, "[$i]unit") ?>
                                <?= $form->field($model, "[$i]quantity") ?>
                            </div>
                        </div>
                    </div>
                    <div class="row input-row margin-bottom">
                        <div class="col-md-3">
                            <?php if (Yii::$app->user->can('requisites.read')) : ?>
                                <?= $form->field($model, "[$i]requisite_id")->widget(BillRequisitesCombo::class) ?>
                            <?php endif ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, "[$i]label") ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, "[$i]time")->widget(DateTimePickerWithFormatter::class) ?>
                        </div>
                    </div>

                    <?php $charges = $model->getCharges(); ?>
                    <?php $charge = reset($charges); ?>
                    <?php if ($charge) : ?>
                        <div class="row input-row">
                            <?= $this->render('../charge/_form', [
                                'form' => $form,
                                'model' => $model,
                                'charges' => $charges,
                                'billTypesList' => $billTypesList,
                                'allowedTypes' => $allowedTypes,
                                'i' => $i,
                            ]) ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php $i++ ?>
    <?php endforeach ?>
</div>

<?php $this->registerJs(<<<JS
(() => {
  let repeatedCharge = null;
  $("#bill-dynamic-form").on("change", ".bill-charges .charge-item input[data-attribute=sum]", function () {
    $(this).closest(".bill-item").find("input[data-bill-sum]").blur();
  });
  const copyObject = function () {
    if (repeatedCharge !== null) {
      return;
    }
    var objectSelectorInputs = $(this).find("[data-object-selector-field]");
    var lengthObjects = objectSelectorInputs.length;
    objectSelectorInputs.each(function (i, elem) {
      var objectInputId = $(elem).attr("id");
      var changerInputId = $(elem).prev("select").attr("id");

      if (!objectInputId || objectInputId.includes("billform")) {
        return;
      }
      initObjectSelectorChanger(changerInputId, objectInputId);

      var objectNumber = getBillAndChargeNumber(objectInputId);
      var objectType = null;
      var objectValue = null;
      if (lengthObjects === 1) {
        var objectType = $("#billform-" + objectNumber.billNumber + "-class").val();
        var objectValue = $("#billform-" + objectNumber.billNumber + "-object_id").val();
        var objectTitle = $("#billform-" + objectNumber.billNumber + "-object_id option:selected").text();
      } else if (lengthObjects === (i + 1)) {
        var objectType = $("#charge-" + objectNumber.billNumber + "-" + i + "-class").val();
        var objectValue = $("#charge-" + objectNumber.billNumber + "-" + i + "-object_id").val();
        var objectTitle = $("#charge-" + objectNumber.billNumber + "-" + i + "-object_id option:selected").text();
      }

      if (objectType && objectValue) {
        $("#" + changerInputId).val(objectType).change();
        setTimeout(() => {
          $("#" + objectInputId).append(new Option(objectTitle, objectValue)).change();
        }, 0);
      }
    });

    function getBillAndChargeNumber(objectInputId) {
      var splitInput = objectInputId.split("-");

      return {"billNumber": splitInput[1], "chargeNumber": splitInput[2]};
    }
  };
  // repeat charge
  const repeatCharge = function (e, newCharge) {
    if (repeatedCharge !== null) {
      $(newCharge).find(":input[id$=label]").val($(repeatedCharge).find(":input[id$=label]").val());
      $(newCharge).find(":input[id$=quantity]").val($(repeatedCharge).find(":input[id$=quantity]").val());
      $(newCharge).find(":input[id^=price_per_unit]").val('');
      $(newCharge).find(":input[id$=sum]").val('');
      // object
      const objectClass = $(repeatedCharge).find(":input[id*=class]").val();
      const objectLabel = $(repeatedCharge).find(":input[id*=object_id] option:selected").text();
      const objectValue = $(repeatedCharge).find(":input[id*=object_id]").val();
      if (objectClass && objectValue) {
        $(newCharge).find(":input[id*=class]").val(objectClass).change();
        setTimeout(() => {
          $(newCharge).find(":input[id*=object_id]").append(new Option(objectLabel, objectValue)).change();
        }, 0);
      }
      // type
      const typeId = $(repeatedCharge).find('div.vue-treeselect').parents('div').eq(0).find('input:hidden').data('value');
      $(newCharge).find('div.vue-treeselect').parents('div').get(0).__vue__.typeChange({id: String(typeId)});
    }
    repeatedCharge = null;
  };
  $(".charges_dynamicform_wrapper").on('beforeInsert', function (e, charge) {
    if (charge.context.classList.contains('repeat-charge')) {
      repeatedCharge = charge.closest('.charge-item').get(0);
    }
  });
  $(".charges_dynamicform_wrapper").on("afterInsert", copyObject).on('afterInsert', repeatCharge);
  $(".bills_dynamicform_wrapper").on("afterInsert", (event, el) => {
    $(el).find(".charges_dynamicform_wrapper").on("afterInsert", copyObject).on('afterInsert', repeatCharge);
  });
})();
JS
) ?>

<?php DynamicFormWidget::end() ?>

<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'),
            ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
