<?php

use hipanel\modules\finance\models\Requisite;
use hipanel\widgets\RefCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var array $countries
 * @var Requisite $model
 */

$this->title = Yii::t('hipanel:finance', 'Create requisite');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel', 'Requisites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin(['id' => 'requisite-form', 'layout' => 'horizontal']) ?>


<?= $this->render('@vendor/hiqdev/hipanel-module-client/src/views/contact/_form', [
    'model' => $model,
    'form' => $form,
    'countries' => $countries,
]) ?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-widget">
            <div class="box-body">
                <?php foreach (Requisite::getTemplatesTypes() as $template) : ?>
                    <?= $form->field($model, "{$template}_id")->widget(RefCombo::class, ['gtype' => "type,document,$template"]) ?>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>

<?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>


