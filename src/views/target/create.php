<?php

use hipanel\modules\finance\models\Target;

/** @var Target $model */
/** @var Target[] $models */

$this->title = Yii::t('hipanel:finance', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', ['models' => $models, 'model' => $model]) ?>