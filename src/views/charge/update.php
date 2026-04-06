<?php

/**
 * @var Bill $bill
 * @var Charge $model
 * @var Charge[] $models
 * @var array $billTypesList
 * @var array $allowedTypes
 */

use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('hipanel:finance', 'Update charges');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['@bill/index']];
$this->params['breadcrumbs'][] = ['label' => $bill->pageTitle, 'url' => ['@bill/view', 'id' => $bill->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
    .add-charge, .remove-charge, .repeat-charge { display: none; }
    .box-tools { display: flex; }
    .box-tools span { padding: 5px 10px; }
');

$this->registerJs(
    <<<'JS'
(() => {
  const formatBillSum = function(additionalValue = 0) {
    const $billSum = $("#bill-sum");
    const currentSum = parseFloat($billSum.attr("data-sum")) || 0;
    const newSum = currentSum + (additionalValue * -1);
    const formattedSum = new Intl.NumberFormat("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(newSum);

    $billSum.text(formattedSum);
  };

  const $chargeSums = $(".charge-item :input[id$=\"-sum\"]");
  
  let totalSum = 0;
  $chargeSums.each(function(i, el) {
    totalSum += parseFloat(el.value) || 0;
  });

  formatBillSum(totalSum);
  $chargeSums.on("change keyup", function(e) {
    let totalSum = 0;
    $chargeSums.each(function(i, el) {
      totalSum += parseFloat(el.value) || 0;
    });
    formatBillSum(totalSum);
  });
})();
JS
);

?>

<?php $form = ActiveForm::begin(['id' => 'charge-update-form']) ?>

<div class="box box-solid">

    <div class="box-header with-border">
        <div class="box-title">
            <?= $bill->pageTitle ?>
        </div>
        <div class="box-tools">
            <span class="bg-gray"><?= Yii::t('hipanel:finance', 'The result bill sum will be:') ?></span>
            <span id="bill-sum" class="bg-maroon-gradient" data-sum="<?= $bill->sum ?>"></span>
        </div>
    </div>

    <div class="box-body">

        <?= $this->render('_form', [
            'model' => $bill,
            'charges' => $models,
            'billTypesList' => $billTypesList,
            'allowedTypes' => $allowedTypes,
            'form' => $form,
            'i' => 0,
        ]) ?>

    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(
            Yii::t('hipanel', 'Cancel'),
            ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']
        ) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
