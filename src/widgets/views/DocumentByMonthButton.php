<?php

use hipanel\widgets\ModalButton;
use kartik\date\DatePicker;
use yii\helpers\Html;

/** @var string $prepend */
/** @var string $append */
/** @var string $buttonLabel */
/** @var string $modalHeader */
/** @var string $modalHeaderColor */
/** @var DateTime $dt */
/** @var \hipanel\modules\finance\models\Purse $model */

?>
<?php $modalButton = ModalButton::begin([
    'id' => sprintf('modal-%s-%s-%s', $model->id, $type, uniqid()),
    'model' => $model,
    'form' => [
        'action' => $action,
        'options' => [
            'class' => 'text-left',
        ]
    ],
    'button' => [
        'label' => $buttonLabel,
        'class' => 'btn btn-default btn-xs',
    ],
    'modal' => [
        'header' => Html::tag('h4', $modalHeader, ['class' => 'modal-title']),
        'headerOptions' => ['class' => $modalHeaderColor],
        'footer' => [
            'label' => $buttonLabel,
            'class' => 'btn btn-default',
            'data-loading-text' => Yii::t('hipanel', 'Updating...'),
        ],
    ],
]) ?>

<?= $prepend ?>
<?= $modalButton->form->field($model, 'month')->widget(DatePicker::class, [
    'options' => [
        'id' => 'purse-month-' . uniqid(),
    ],
    'pluginOptions' => [
        'format' => "yyyy-mm",
        'viewMode' => 'months',
        'minViewMode' => 'months',
        'autoclose' => true,
        'endDate' => $dt->modify('last day of this month')->format('d-m-Y'),
    ],
]) ?>
<?= $append ?>

<?php ModalButton::end() ?>
