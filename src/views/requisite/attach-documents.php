<?php

/*
 * Client module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-client
 * @package   hipanel-module-client
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

use hipanel\modules\document\widgets\StackedDocumentsView;
use hipanel\widgets\Box;
use hipanel\widgets\FileInput;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\ActiveForm;

/**
 * @var \hipanel\modules\client\models\Contact
 * @var \hipanel\modules\client\models\DocumentUploadForm $model
 */
$this->title = Yii::t('hipanel:client', 'Attached documents');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:client', 'Contacts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => Inflector::titleize($contact->name, true),
    'url' => ['view', 'id' => $contact->id],
];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-5">
        <?php Box::begin(['title' => Yii::t('hipanel:client', 'Attach new documents')]); ?>

        <p>
            <?= Yii::t('hipanel:client',
                'You can upload copy of your documents in order to help us verify your identity') ?>
        </p>

        <p>
            <?= Yii::t('hipanel:client', 'Please make sure that your submitted documents are:') ?>
        </p>
        <ul>
            <li><?= Yii::t('hipanel:client', 'High quality: color image, 150 DPI resolution or higher, not blurry') ?></li>
            <li><?= Yii::t('hipanel:client', 'Entirely visible: all four corners are captured') ?></li>
            <li><?= Yii::t('hipanel:client', 'Valid: the expiry date is clearly visible') ?></li>
        </ul>

        <?php $form = ActiveForm::begin([
            'id' => 'attach-form',
            'enableClientValidation' => true,
            'validateOnBlur' => true,
            'options' => ['enctype' => 'multipart/form-data'],
        ]);

        echo $form->field($model, 'id')->hiddenInput()->label(false);
        echo $form->field($model, 'title')->textInput([
            'placeholder' => Yii::t('hipanel:client', 'Passport, ID card, etc'),
        ]);
        echo $form->field($model, 'file')->widget(FileInput::class, [
            'pluginOptions' => [
                'previewFileType' => 'any',
                'showRemove' => true,
                'showUpload' => false,
            ],
        ])->label(false); ?>

        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']); ?>

        <?php $form->end(); ?>
        <?php Box::end(); ?>
    </div>
    <div class="col-md-7">
        <?php $box = Box::begin(['renderBody' => false]) ?>
            <?php $box->beginHeader() ?>
                <?= $box->renderTitle(Yii::t('hipanel:client', 'Documents')) ?>
            <?php $box->endHeader() ?>
            <?php $box->beginBody() ?>
                <?= StackedDocumentsView::widget([
                    'models' => $contact->documents,
                    'thumbSize' => 64,
                ]); ?>
            <?php $box->endBody() ?>
        <?php $box->end() ?>
    </div>
</div>

