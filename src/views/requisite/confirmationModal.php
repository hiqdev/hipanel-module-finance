<?php

use hipanel\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var \yii\web\View
 * @var \hipanel\modules\client\models\Contact $contact
 * @var \hipanel\modules\client\forms\PhoneConfirmationForm $model
 * @var \hipanel\modules\client\models\NotifyTries $tries
 */
$requestCodeButton = Html::button(Yii::t('hipanel:client', 'Request code'), [
    'id' => 'request-code',
    'class' => 'pull-right btn btn-block btn-info ' . (!$tries->isIntervalSatisfied() ? 'disabled' : ''),
    'style' => 'margin-top: 25px;',
    'data' => [
        'url' => Url::to(['@contact/request-phone-confirmation-code']),
        'loading-text' => Yii::t('hipanel:client', 'Requesting...'),
    ],
]);
$submitButton = Html::submitButton(Yii::t('hipanel', 'Confirm'), [
    'class' => 'btn btn-success btn-block',
    'style' => 'margin-top: 25px;',
    'data' => [
        'loading-text' => Yii::t('hipanel:client', 'Checking...'),
    ],
]);
?>

<?php $form = ActiveForm::begin([
    'action' => ['@contact/confirm-phone', 'id' => $model->id, 'type' => $model->type],
    'options' => [
        'id' => 'confirmation-form',
    ],
]) ?>

    <p>
        <?= Yii::t('hipanel:client', 'Phone confirmation is a simple procedure that helps us to verify your identity. Press the "Request code" button bellow to get SMS message with confirmation code. Enter the code from a message and press "Confirm" button to complete the phone confirmation procedure.') ?>
    </p>

<?= Html::activeHiddenInput($model, 'id', ['class' => 'confirmation-form-id']) ?>
<?= Html::activeHiddenInput($model, 'type') ?>
    <div class="row">
        <div class="col-md-8"><?= $form->field($model, 'phone')->textInput(['readonly' => true]) ?></div>
        <div class="col-md-4"><?= $requestCodeButton ?></div>
    </div>
    <div class="row">
        <div class="col-md-8"><?= $form->field($model, 'code') ?></div>
        <div class="col-md-4"><?= $submitButton ?></div>
    </div>

<?php if (!$tries->isIntervalSatisfied()) : ?>
    <p class="docs-next-try">
        <?= Yii::t('hipanel:client', 'We have sent the confirmation code on your phone number.') ?>

        <?= Yii::t('hipanel:client',
            'Usually we deliver SMS immediately, but sometimes it takes a bit longer. In case you have not received the code, you can request a new one {time}',
            [
                'time' => Html::tag('span', '', ['data-seconds' => $tries->sleep]),
            ]) ?>
    </p>
<?php endif ?>

<?php $form->end() ?>


<?php $this->registerJs(<<<JS
(function () {
    var requestButton = $('#request-code');
    var form = $('#confirmation-form');
    var nextTryBlock = $('.docs-next-try');
    
    // Init attributes
    requestButton.filter('.disabled').attr('disabled', true);
    
    var enableRequestButton = function () {
        return requestButton.removeClass('disabled').removeAttr('disabled')
    };
    
    requestButton.on('click', function (event) {
        event.preventDefault();
        if ($(this).hasClass('disabled')) {
            return false;
        }

        $.post({
            url: $(this).data('url'),
            data: form.serialize(),
            beforeSend: function () {
                $(this).button('loading');
            }.bind(this),
            success: function (response) {
                if (response.success) {
                    return form.trigger('reload');
                }
                
                hipanel.notify.error(response.error);
                $(this).button('reset');
            }
        });
    });
    
    form.on('reload', function () {
        var modalBody = form.parent();
        $.get({
            url: modalBody.data('action-url'),
            beforeSend: function () {
                modalBody.html(hipanel.loadingBar());
            },
            success: function (html) {
                modalBody.html(html);
            }
        });
    });
    
    form.on('beforeSubmit', function (event) {
        event.preventDefault();
        var submitButton = $(this).find('button[type="submit"]');
        
        $.post({
            url: $(this).attr('action'),
            data: form.serialize(),
            beforeSend: function () {
                submitButton.button('loading');
            }.bind(this),
            success: function (response) {
                if (response.success) {
                    hipanel.notify.success(response.success);
                    location.reload();
                    return;
                }
                
                hipanel.notify.error(response.error);
                submitButton.button('reset');
            }
        });
        
        return false;
    });
    
    if (nextTryBlock.length) {
        var nextTry = nextTryBlock.find('span');
        var nextTryMoment = moment().add(nextTry.data('seconds'), 'second');
        var updateNextTry = function () {
            if (nextTryMoment.diff(moment()) > 0) {
                nextTry.text(nextTryMoment.fromNow());
            } else {
                nextTry.text('');
                enableRequestButton();
                clearInterval(intervalId);
            }
        };
        updateNextTry();
        var intervalId = setInterval(updateNextTry, 1000);
    }
})();

JS
);
