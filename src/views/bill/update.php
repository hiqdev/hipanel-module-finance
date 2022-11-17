
<?php

use yii\web\View;
use hipanel\modules\ticket\models\Thread;

/**
 * @var View $this
 * @var Thread $models
 * @var array $billTypesList
 */

$this->title = Yii::t('hipanel:finance', 'Update payments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="bill-update">
    <?= $this->render('_form', [
        'models' => $models,
        'billTypesList' => $billTypesList,
    ]) ?>
</div>
