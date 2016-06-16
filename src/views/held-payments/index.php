<?php

use hipanel\base\View;
use hipanel\modules\finance\grid\ChangeGridView;
use hipanel\modules\finance\grid\HeldPaymentsGridView;
use hipanel\modules\finance\models\Change;
use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexLayoutSwitcher;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use hiqdev\hiart\ActiveDataProvider;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/**
 * @var View $this
 * @var array $states
 * @var Change $model
 * @var ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('hipanel/finance/change', 'Pending confirmation payments');
$this->subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->breadcrumbs->setItems([$this->title]); ?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])); ?>
<?php $page = IndexPage::begin([
    'model' => $model,
    'dataProvider' => $dataProvider,
]) ?>
    <?= $page->setSearchFormData(compact(['states'])) ?>
    <?php $page->beginContent('main-actions') ?>
        <?php // TODO: add actions ?>
    <?php $page->endContent() ?>
    <?php $page->beginContent('show-actions') ?>
        <?= IndexLayoutSwitcher::widget() ?>
        <?= $page->renderSorter([
            'attributes' => [
                'client',
                'time'
            ],
        ]) ?>

        <?= $page->renderPerPage() ?>
    <?php $page->endContent() ?>
    <?php $page->beginContent('bulk-actions') ?>
        <?php if ($model->state === $model::STATE_NEW) : ?>
            <div>
                <?= AjaxModal::widget([
                    'id' => 'bulk-approve-modal',
                    'bulkPage' => true,
                    'header'=> Html::tag('h4', Yii::t('hipanel/finance/change', 'Approve'), ['class' => 'modal-title']),
                    'scenario' => 'bulk-approve',
                    'actionUrl' => ['bulk-approve-modal'],
                    'size' => Modal::SIZE_LARGE,
                    'handleSubmit' => false,
                    'toggleButton' => [
                        'class' => 'btn btn-success btn-sm',
                        'label' => Yii::t('hipanel/finance/change', 'Approve')
                    ],
                ]) ?>
                <?= AjaxModal::widget([
                    'id' => 'bulk-reject-modal',
                    'bulkPage' => true,
                    'header'=> Html::tag('h4', Yii::t('hipanel/finance/change', 'Reject'), ['class' => 'modal-title ']),
                    'scenario' => 'bulk-reject',
                    'actionUrl' => ['bulk-reject-modal'],
                    'size' => Modal::SIZE_LARGE,
                    'handleSubmit' => false,
                    'toggleButton' => [
                        'class' => 'btn btn-danger btn-sm',
                        'label' => Yii::t('hipanel/finance/change', 'Reject')
                    ],
                ]) ?>
            </div>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm(); ?>
            <?= HeldPaymentsGridView::widget([
                'dataProvider' => $dataProvider,
                'boxed' => false,
                'filterModel' => $model,
                'columns' => [
                    'checkbox',
                    'client',
                    'user_comment',
                    'txn',
                    'label',
                    'amount',
                    'time',
                ]
            ]); ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>

<?php Pjax::end() ?>
