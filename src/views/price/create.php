<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;

$this->title = Yii::t('hipanel:finance', 'Create prices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var \yii\web\View $this
 * @var Price[] $models
 * @var Price $model
 * @var Plan $plan
 */

?>

<?= $this->render('_form', compact('models', 'model', 'plan')) ?>
