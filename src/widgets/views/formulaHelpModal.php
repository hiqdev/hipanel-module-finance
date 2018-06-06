<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\widgets\FormulaHelpModal $widget
 */

$widget = $this->context;

?>

<?php Modal::begin([
    'toggleButton' => false,
    'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Formula usage examples')),
    'size' => Modal::SIZE_LARGE,
    'id' => $widget->getId()
]); ?>
<?= Html::beginTag('div', ['class' => 'table-responsive']) ?>

<?php $exampleGroups = $widget->formulaExamplesProvider()->getGroups() ?>
<?php foreach ($exampleGroups as $group) : ?>
    <?= \hiqdev\higrid\GridView::widget([
        'tableOptions' => ['class' => 'table'],
        'summary' => '<h4>' . $group['name'] . '</h4>',
        'showHeader' => false,
        'dataProvider' => new \yii\data\ArrayDataProvider([
            'allModels' => $group['formulas'],
            'sort' => false,
            'pagination' => false,
        ]),
        'columns' => [
            [
                'format' => 'raw',
                'value' => function ($formula) {
                    return '<kbd class="javascript">' . $formula . '</kbd>';
                }
            ]
        ]
    ]) ?>
<?php endforeach; ?>

<?= Html::endTag('div'); ?>
<?php Modal::end() ?>
