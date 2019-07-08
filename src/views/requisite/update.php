<?php
/**
 * Client module for HiPanel.
 *
 * @link      https://github.com/hiqdev/hipanel-module-client
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */
use hipanel\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Inflector;

/**
 * @var string
 * @var array $countries
 * @var boolean $askPincode
 * @var \hipanel\modules\client\models\Contact $model
 */
$this->title = Yii::t('hipanel', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:client', 'Contacts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => Inflector::titleize($model->getName(), true),
    'url' => ['view', 'id' => $model->id],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin([
    'id' => 'contact-form',
    'action' => $action ?: $model->scenario,
    'enableClientValidation' => true,
    'validateOnBlur' => true,
    'enableAjaxValidation' => true,
    'layout' => 'horizontal',
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>

<?= \hipanel\modules\client\widgets\GdprConsent::widget(compact('model', 'form')) ?>
<?= $this->render('_form', compact('model', 'countries', 'model', 'form')) ?>

<?php ActiveForm::end() ?>

<?= $this->render('_pincode', compact('askPincode')) ?>
