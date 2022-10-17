<?php

/**
 * @var yii\web\View
 * @var $models \hipanel\modules\finance\models\Bill[]
 * @var $billTypes array
 * @var $billTypesList array
 * @var $billGroupLabels array
 */
$this->title = Yii::t('hipanel:finance', 'Create payment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="bill-create">
    <?= $this->render('_form', [
        'models' => $models,
        'billTypes' => $billTypes,
        'billTypesList' => $billTypesList,
        'billGroupLabels' => $billGroupLabels,
    ]) ?>
</div>
