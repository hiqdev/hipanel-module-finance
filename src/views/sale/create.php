<?php

use hipanel\modules\finance\models\Sale;
use yii\helpers\Html;

/** @var Sale $model */
/** @var Sale[] $models */

$this->title = Yii::t('hipanel:finance:sale', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance:sale', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', ['models' => $models, 'model' => $model]) ?>
