<?php
/* @var $this yii\web\View */
/* @var $model hipanel\modules\ticket\models\Thread */
/* @var $type string */

$this->title = Yii::t('hipanel/finance', 'Create payment');
$this->breadcrumbs->setItems([['label' => Yii::t('hipanel/finance', 'Payments'), 'url' => ['index']]]);
$this->breadcrumbs->setItems([$this->title]);
?>

<div class="bill-create">
    <?= $this->render('_form', [
        'models' => $models,
        'billTypes' => $billTypes,
        'billGroupLabels' => $billGroupLabels,
    ]) ?>
</div>
