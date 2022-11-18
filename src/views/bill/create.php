<?php

use hipanel\modules\finance\models\Bill;
use yii\web\View;

/**
 * @var View $this
 * @var Bill[] $models
 * @var array $billTypesList
 */

$this->title = Yii::t('hipanel:finance', 'Create payment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="bill-create">
    <?= $this->render('_form', [
        'models' => $models,
        'billTypesList' => $billTypesList,
    ]) ?>
</div>
