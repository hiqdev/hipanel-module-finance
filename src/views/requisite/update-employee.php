<?php
/**
 * Client module for HiPanel.
 *
 * @link      https://github.com/hiqdev/hipanel-module-client
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */
use hipanel\modules\client\forms\EmployeeForm;
use hipanel\modules\client\models\Contact;
use yii\bootstrap\ActiveForm;
use yii\helpers\Inflector;

/**
 * @var string
 * @var array $countries
 * @var boolean $askPincode
 * @var Contact $model the primary contact
 * @var EmployeeForm $employeeForm
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
    'action' => ['update-employee', 'id' => $model->id],
    'enableClientValidation' => true,
    'validateOnBlur' => true,
    'layout' => 'horizontal',
]) ?>

<?= $this->render('_employee-form', compact('scenario', 'countries', 'model', 'form', 'employeeForm')) ?>

<?php ActiveForm::end() ?>

<?= $this->render('_pincode', compact('askPincode')) ?>
