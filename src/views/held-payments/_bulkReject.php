<?php
use hipanel\helpers\Url;
use hipanel\modules\finance\grid\HeldPaymentsGridView;
use hipanel\modules\finance\models\Change;
use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/**
 * @var Change[]
 */
?>
<?php $form = ActiveForm::begin([
    'id' => 'bulk-reject-form',
    'action' => Url::toRoute('bulk-reject'),
    'enableAjaxValidation' => false,
]) ?>

<div class="panel panel-default">
    <div class="panel-heading"><?= Yii::t('hipanel:finance:change', 'Affected payments') ?></div>
    <div class="panel-body">
            <?= HeldPaymentsGridView::widget([
                'dataProvider' => new ArrayDataProvider(['allModels' => $models, 'pagination' => false]),
                'boxed' => false,
                'columns' => [
                    'client',
                    'user_comment',
                    'txn',
                    'label',
                    'amount',
                ],
                'layout' => '{items}',
            ]) ?>
    </div>
</div>

<?php foreach ($models as $item) : ?>
    <?= Html::activeHiddenInput($item, "[$item->id]id") ?>
<?php endforeach; ?>

<div class="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'comment')->textInput([
            'id' => 'change-reject-comment',
            'name' => 'comment',
        ]); ?>
    </div>
</div>

<hr>
<?= Html::submitButton(Yii::t('hipanel:finance:change', 'Reject'), ['class' => 'btn btn-danger']) ?>

<?php ActiveForm::end() ?>
