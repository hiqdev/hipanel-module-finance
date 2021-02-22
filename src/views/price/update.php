<?php

$this->title = Yii::t('hipanel', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', compact('models', 'model')) ?>
    </div>
</div>
