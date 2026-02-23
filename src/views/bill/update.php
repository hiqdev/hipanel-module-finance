<?php

use hipanel\modules\finance\forms\BillForm;
use yii\web\View;

/**
 * @var View $this
 * @var BillForm $models
 * @var array $billTypesList
 * @var array $allowedTypes
 */

$this->title = Yii::t('hipanel:finance', 'Update payments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="bill-update">
    <?= $this->render('_form', [
        'model' => reset($models),
        'models' => $models,
        'billTypesList' => $billTypesList,
        'allowedTypes' => $allowedTypes,
    ]) ?>
</div>
