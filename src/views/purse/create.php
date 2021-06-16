
<?php

/**
 * @var yii\web\View
 * @var $models hipanel\modules\finance\models\Purse
 */
$this->title = Yii::t('hipanel:finance', 'Create purse');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Purses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="bill-update">
    <?= $this->render('_form', [
        'models' => $models,
    ]) ?>
</div>
