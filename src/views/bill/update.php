
<?php

/**
 * @var yii\web\View
 * @var $models hipanel\modules\ticket\models\Thread
 * @var $billTypes array
 * @var $billTypesList array
 * @var $billGroupLabels array
 */
$this->title = Yii::t('hipanel:finance', 'Update payments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="bill-update">
    <?= $this->render('_form', [
        'models' => $models,
        'billTypes' => $billTypes,
        'billGroupLabels' => $billGroupLabels,
        'billTypesList' => $billTypesList,
    ]) ?>
</div>
