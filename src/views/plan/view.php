<?php

use yii\helpers\Html;


/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 */

$this->title = Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .profile-block {
        text-align: center;
    }
");

?>

<?php if ($model->type === 'server'): ?>
    <?= $this->render('view/_server', compact('model')) ?>
<?php else: ?>
    <h2><?= Yii::t('hipanel.finance.plan', 'This plan type editing is not implemented yet') ?></h2>
<?php endif ?>
