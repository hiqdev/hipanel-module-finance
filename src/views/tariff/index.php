<?php

use hipanel\modules\finance\grid\TariffGridView;
use hipanel\widgets\IndexLayoutSwitcher;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\bootstrap\Dropdown;

$this->title = Yii::t('app', 'Tariffs');
$this->subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['breadcrumbs'][] = $this->title;

?>


<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
<?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

<?= $page->setSearchFormData(compact(['paymentType'])) ?>

<?php $page->beginContent('main-actions') ?>
<?php if (Yii::$app->user->can('manage')) : ?>
    <div class="dropdown">
        <a class="btn btn-sm btn-success dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown"
           aria-haspopup="true" aria-expanded="true">
            <?= Yii::t('hipanel', 'Create'); ?>
            <span class="caret"></span>
        </a>
        <?= Dropdown::widget([
            'items' => [
                ['label' => Yii::t('app', 'Create tariff for domain(s)'), 'url' => '#'],
                ['label' => Yii::t('app', 'Create svds tariff'), 'url' => '#'],
                ['label' => Yii::t('app', 'Create ovds tariff'), 'url' => '#'],
                ['label' => Yii::t('app', 'Create server tariff'), 'url' => '#'],
                ['label' => Yii::t('app', 'Create resources tariff'), 'url' => '#'],
            ],
        ]); ?>
    </div>
<?php endif; ?>
<?php $page->endContent() ?>

<?php $page->beginContent('show-actions') ?>
<?= IndexLayoutSwitcher::widget() ?>

<?= $page->renderSorter([
    'attributes' => [
        'seller',
        'client',
        'tariff',
    ],
]) ?>
<?= $page->renderPerPage() ?>
<?php $page->endContent() ?>

<?php $page->beginContent('bulk-actions') ?>
<?php if (Yii::$app->user->can('manage')) : ?>
    <?= $page->renderBulkButton(Yii::t('app', 'Delete'), 'delete', 'danger'); ?>
<?php endif; ?>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
<?php $page->beginBulkForm() ?>
<?= TariffGridView::widget([
    'boxed' => false,
    'dataProvider' => $dataProvider,
    'filterModel' => $model,
    'columns' => Yii::$app->user->can('manage')
        ? [
            'checkbox',
            'tariff', 'note', 'used',
            'client_id', 'seller_id',
        ]
        : [
            'tariff', 'note', 'used',
            'client_id', 'seller_id',
        ],
]) ?>
<?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
<?php $page->end() ?>
<?php Pjax::end() ?>
