<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use yii\helpers\Html;

$this->title = Yii::t('hipanel.finance.price', 'Create suggested prices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $plan->name, 'url' => ['@plan/view', 'id' => $plan->id]];
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var \yii\web\View $this
 * @var Price[] $models
 * @var Price $model
 * @var Plan $plan
 * @var string $type
 */

?>

<?php if (!empty($models)) : ?>
    <?= $this->render('_form', compact('models', 'model', 'plan')) ?>
<?php else: ?>
    <?php $box = \hipanel\widgets\Box::begin([
        'title' => Yii::t('hipanel.finance.price', 'No price suggestions for this object'),
    ]) ?>
    <?= Yii::t('hipanel.finance.price', 'We could not suggest any new prices of type "{suggestionType}" for the selected object. Probably, they were already created earlier or this suggestion type is not compatible with this object type', [
            'suggestionType' => Yii::t('hipanel.finance.suggestionTypes', $type),
        ]) ?>

    <br/>

    <?= Yii::t('hipanel.finance.price', 'You can return back to plan {backToPlan}', [
        'backToPlan' => Html::a($plan->name, Url::to(['@plan/view', 'id' => $plan->id])),
    ]) ?>
    <?= $box->endBody(); ?>
<?php endif ?>
