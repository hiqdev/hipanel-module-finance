<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('hipanel.finance.price', 'Update prices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariff plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $plan->name, 'url' => ['view', 'id' => $plan->id]];
$this->params['breadcrumbs'][] = $this->title;

$models = $grouper->group();

?>

<?php $form = ActiveForm::begin([
    'id' => 'prices-form',
    'action' => ['update-prices', 'id' => $plan->id, 'scenario' => 'update'],
]) ?>

<div class="box box-widget">
    <div class="box-body">
        <?php $idx = 0; ?>
        <?php foreach ($models as $model) : ?>
            <?= Html::activeHiddenInput($model, "[$idx]id", ['value' => $model->id]) ?>
            <?= $this->render('../../price/formRow/simple', ['model' => $model, 'plan' => $plan, 'form' => $form, 'i' => $idx]) ?>
            <?php $idx++ ?>
        <?php endforeach ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>
<?php ActiveForm::end() ?>

