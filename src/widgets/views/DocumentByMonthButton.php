<?php

use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\widgets\BankAccountField;
use hipanel\widgets\ModalButton;
use hipanel\widgets\MonthPicker;
use yii\helpers\Html;
use yii\web\View;

/** @var string $prepend */
/** @var string $append */
/** @var string $buttonLabel */
/** @var string $modalHeader */
/** @var string $modalHeaderColor */
/** @var DateTime $dt */
/** @var Purse $model */
/** @var string $type */
/** @var array $action */
/** @var View $this */

?>

<?php $modalButton = ModalButton::begin([
    'id' => sprintf('modal-%s-%s-%s', $model->id, $type, uniqid()),
    'model' => $model,
    'form' => [
        'action' => $action,
        'options' => [
            'class' => 'text-left',
        ],
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

<?= $modalButton->form->field($model, 'month')->widget(MonthPicker::class, [
    'options' => [
        'id' => 'purse-month-' . $this->context->id,
    ],
    'clientOptions' => [
        'dateFormat' => 'Y-m',
        'maxDate' => $dt->modify('next month')->format('Y-m'),
    ],
]) ?>

<?= BankAccountField::widget(['purse' => $model, 'contact' => $model->requisite, 'form' => $modalButton->form]) ?>
<?= BankAccountField::widget(['purse' => $model, 'contact' => $model->contact, 'form' => $modalButton->form]) ?>

<?= $append ?>

<?php ModalButton::end() ?>
