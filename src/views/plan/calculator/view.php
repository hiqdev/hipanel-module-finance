<?php

use hipanel\assets\StickySidebarAsset;
use hipanel\modules\finance\grid\SalesInPlanGridView;
use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\widgets\CreateCalculatorPricesButton;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var IndexPage $page */
/** @var Plan $model */
/** @var PlanInternalsGrouper $grouper */
/** @var array $tabs */

StickySidebarAsset::register($this);

$this->registerCss(<<<CSS
.box-header-wrapper > .box-header {
    background-color: #fff;
    z-index: 999;
}
CSS
);

$this->registerJs(<<<JS
const stickySidebar = new StickySidebar('.box-widget > .box-header-wrapper', {
    topSpacing: 0,
    containerSelector: '.col > .row-md-12',
    innerWrapperSelector: '.box-header-wrapper > .box-header'
});
$(document).on('pjax:end', stickySidebar.updateSticky);
JS
);

$tabs = $grouper->tabs;
?>

<?php $page->beginContent('title') ?>
    <?= Yii::t('hipanel:finance', 'Prices') ?>
<?php $page->endContent() ?>

<?php $page->beginContent('actions') ?>
    <?php if (Yii::$app->user->can('plan.create')) : ?>
        <?= CreateCalculatorPricesButton::widget(compact('model')) ?>
    <?php endif ?>
    <?php if (Yii::$app->user->can('price.update')) : ?>
        <?= $page->renderBulkButton('@price/update', Yii::t('hipanel', 'Update'), ['color' => 'warning']) ?>
    <?php endif ?>
    <?php if (Yii::$app->user->can('price.delete')) : ?>
        <?= $page->renderBulkDeleteButton('@price/delete') ?>
    <?php endif ?>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>

            <?= GridView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $grouper->group(), // calculator tabs
                    'pagination' => false,
                ]),
                'layout' => '{items}',
                'showHeader' => false,
                'rowOptions' => ['class' => 'info'],
                'columns' => [
                    'label' => [
                        'attribute' => 'label',
                        'format' => 'raw',
                        'value' => static function($tab): string {
                            $label = Html::tag('strong', $tab['label'], ['style' => ['text-transform' => 'uppercase']]);
                            $button = ''; // todo: Html::button(Yii::t('hipanel', 'Check all price for this tab'), ['class' => 'btn btn-xs']);

                            return Html::tag('span', $label . $button, ['style' => 'display: flex; justify-content: space-between;']);
                        },
                    ],
                ],
                'afterRow' => static fn(array $tab): string => SalesInPlanGridView::widget([
                        'boxed' => false,
                        'showHeader' => false,
                        'layout' => '<td style="padding: 0;">{items}</td>',
                        'pricesBySoldObject' => $tab['groups'][1],
                        'dataProvider' => new ArrayDataProvider([
                            'allModels' => $tab['groups'][0],
                            'pagination' => false,
                        ]),
                        'summaryRenderer' => static fn() => '',
                        'columns' => [
                            'object_link',
                            'object_label' => [
                                'attribute' => 'object_label',
                                'value' => function (Sale $sale, int $key) use ($tabs): string {
                                    foreach ($tabs['groups'][1][$key] ?? [] as $price) {
                                        if ($price->object->id === $sale->object_id) {
                                            return $price->object->label;
                                        }
                                    }

                                    return '';
                                },
                            ],
                            'seller',
                            'buyer',
                            'time',
                            'price_related_actions',
                            'estimate_placeholder',
                        ],
                    ]),
            ]) ?>

    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
