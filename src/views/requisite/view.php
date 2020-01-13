<?php

use hipanel\modules\client\grid\ContactGridView;
use hipanel\modules\finance\menus\RequisiteDetailMenu;
use hipanel\modules\client\widgets\ForceVerificationBlock;
use hipanel\modules\document\widgets\StackedDocumentsView;
use hipanel\widgets\Box;
use hipanel\widgets\ClientSellerLink;
use hiqdev\assets\flagiconcss\FlagIconCssAsset;
use yii\base\ViewNotFoundException;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * @var \hipanel\modules\client\models\Contact
 */
$this->title = Inflector::titleize($model->name, true);
$this->params['subtitle'] = Yii::t('hipanel:client', 'Requisite detailed information') . ' #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:client', 'Requisites'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

FlagIconCssAsset::register($this);

?>

<div class="row">
    <div class="col-md-3">
        <?php Box::begin([
            'options' => [
                'class' => 'box-solid',
            ],
            'bodyOptions' => [
                'class' => 'no-padding',
            ],
        ]) ?>
            <?php try {
            ?>
                <div class="profile-user-img text-center">
                    <?= $this->render('//layouts/gravatar', ['email' => $model->email, 'size' => 120]) ?>
                </div>
            <?php
        } catch (ViewNotFoundException $e) {
            ?>
            <?php
        } ?>
            <p class="text-center">
                <span class="profile-user-role"><?= $this->title ?></span>
                <br>
                <span class="profile-user-name"><?= ClientSellerLink::widget(['model' => $model]) ?></span>
            </p>

            <div class="profile-usermenu">
                <?= RequisiteDetailMenu::widget(['model' => $model]) ?>
            </div>
        <?php Box::end() ?>
    </div>

    <div class="col-md-9">
        <div class="row">
            <div class="col-md-6">
                <?php $box = Box::begin(['renderBody' => false]) ?>
                    <?php $box->beginHeader() ?>
                        <?= $box->renderTitle(Yii::t('hipanel:client', 'Contact information')) ?>
                        <?php $box->beginTools() ?>
                            <?= Html::a(Yii::t('hipanel', 'Edit'), ['@contact/update', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']) ?>
                        <?php $box->endTools() ?>
                    <?php $box->endHeader() ?>
                    <?php $box->beginBody() ?>
                        <?= ContactGridView::detailView([
                            'boxed'   => false,
                            'model'   => $model,
                            'columns' => [
                                'seller_id', 'client_id',
                                'name_with_verification',
                                'birth_date',
                                'email_with_verification', 'abuse_email',
                                'voice_phone', 'fax_phone',
                            ],
                        ]) ?>
                    <?php $box->endBody() ?>
                <?php $box->end() ?>

                <?php $box = Box::begin(['renderBody' => false]) ?>
                    <?php $box->beginHeader() ?>
                        <?= $box->renderTitle(Yii::t('hipanel:client', 'Postal information')) ?>
                    <?php $box->endHeader() ?>
                    <?php $box->beginBody() ?>
                        <?= ContactGridView::detailView([
                            'boxed'   => false,
                            'model'   => $model,
                            'columns' => [
                                'first_name', 'last_name', 'organization',
                                'street', 'city', 'province', 'postal_code', 'country',
                            ],
                        ]) ?>
                    <?php $box->endBody() ?>
                <?php $box->end() ?>


                <?php $box = Box::begin([
                    'renderBody' => false,
                    'collapsed' => $model->isEmpty(['reg_data', 'vat_number', 'vat_rate', 'invoice_last_no']),
                    'collapsable' => true,
                    'title' => Yii::t('hipanel:client', 'Registration data'),
                ]) ?>
                    <?php $box->beginBody() ?>
                        <?= ContactGridView::detailView([
                            'boxed'   => false,
                            'model'   => $model,
                            'columns' => ['reg_data', 'vat_rate', 'invoice_last_no'],
                        ]) ?>
                    <?php $box->endBody() ?>
                <?php $box->end() ?>

                <?php $box = Box::begin([
                    'renderBody' => false,
                    'collapsed' => $model->isEmpty('bank_details'),
                    'collapsable' => true,
                    'title' => Yii::t('hipanel:client', 'Bank details'),
                ]) ?>
                    <?php $box->beginBody() ?>
                        <?= ContactGridView::detailView([
                            'boxed'   => false,
                            'model'   => $model,
                            'columns' => ['bank_account', 'bank_name', 'bank_address', 'bank_swift'],
                        ]) ?>
                    <?php $box->endBody() ?>
                <?php $box->end() ?>

                <?php $box = Box::begin([
                    'renderBody' => false,
                    'collapsed' => true,
                    'title' => Yii::t('hipanel:client', 'Additional information'),
                ]) ?>
                    <?php $box->beginBody() ?>
                        <?= ContactGridView::detailView([
                            'boxed'   => false,
                            'model'   => $model,
                            'columns' => [
                                'passport_date', 'passport_no', 'passport_by',
                                'organization_ru', 'inn', 'kpp', 'director_name', 'isresident',
                            ],
                        ]) ?>
                    <?php $box->endBody() ?>
                <?php $box->end() ?>
            </div>

            <div class="col-md-6">
                <?php if (Yii::getAlias('@document', false) !== false && Yii::$app->user->can('document.read')) : ?>
                    <?php $box = Box::begin(['renderBody' => false]) ?>
                        <?php $box->beginHeader() ?>
                            <?= $box->renderTitle(Yii::t('hipanel:client', 'Documents')) ?>
                            <?php $box->beginTools() ?>
                                <?= Html::a(Yii::t('hipanel', 'Details'), ['@contact/attach-documents', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']) ?>
                                <?= Html::a(Yii::t('hipanel', 'Upload'), ['@contact/attach-documents', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']) ?>
                            <?php $box->endTools() ?>
                        <?php $box->endHeader() ?>
                        <?php $box->beginBody() ?>
                            <?= StackedDocumentsView::widget([
                                'models' => $model->documents,
                            ]); ?>
                        <?php $box->endBody() ?>
                    <?php $box->end() ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
