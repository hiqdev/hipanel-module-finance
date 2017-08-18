<?php

use hipanel\modules\finance\grid\TariffGridView;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\bootstrap\Dropdown;

/**
 * @var \yii\web\View
 * @var array $types
 */
$this->title = Yii::t('hipanel', 'Tariffs');
$this->params['subtitle'] = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
<?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

    <?php $page->setSearchFormData(compact(['types'])) ?>

    <?php $page->beginContent('main-actions') ?>
        <?php if (Yii::$app->user->can('manage')) : ?>
            <div class="dropdown">
                <a class="btn btn-sm btn-success dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <?= Yii::t('hipanel', 'Create') ?>
                    <span class="caret"></span>
                </a>
                <?= Dropdown::widget([
                    'items' => array_filter([
                        ['label' => Yii::t('hipanel:finance:tariff', 'Create domain tariff'), 'url' => ['@tariff/create-domain']],
                        ['label' => Yii::t('hipanel:finance:tariff', 'Create SSD VDS tariff'), 'url' => ['@tariff/create-svds']],
                        ['label' => Yii::t('hipanel:finance:tariff', 'Create OpenVZ tariff'), 'url' => ['@tariff/create-ovds']],
                        ['label' => Yii::t('hipanel:finance:tariff', 'Create server tariff'), 'url' => ['@tariff/create-server']],
                        Yii::getAlias('@certificate', false)
                            ? ['label' => Yii::t('hipanel:finance:tariff', 'Create certificate tariff'), 'url' => ['@tariff/create-certificate']]
                            : null,
                    ]),
                ]) ?>
            </div>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('show-actions') ?>
        <?= $page->renderLayoutSwitcher() ?>
        <?= $page->renderSorter([
            'attributes' => [
                'seller', 'client',
                'tariff',
            ],
        ]) ?>
        <?= $page->renderPerPage() ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('manage')) : ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Copy'), 'copy', 'default') ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Delete'), 'delete', 'danger') ?>
        <?php endif ?>
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
                    'tariff', 'used', 'type',
                    'client_id', 'seller_id',
                ]
                : [
                    'tariff', 'used',
                    'client_id', 'seller_id',
                ],
        ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>
<?php Pjax::end() ?>
