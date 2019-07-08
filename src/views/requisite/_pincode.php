<?php

use yii\web\View;

/**
 * @var View
 * @var boolean $askPincode
 */
$notClient = !(bool) Yii::$app->user->can('role:client');
?>

<?php $this->registerJs(<<<"JS"
(function ($) { 
    var oldEmail = $('#contact-oldemail'),
        notClient = Boolean('{$notClient}'),
        askPincode = Boolean('{$askPincode}'), 
        form = $('#contact-form'),
        pincodeInput = $('#contact-pincode')
    
    form.on('beforeSubmit', function(event, attributes, messages, deferreds) {
        var show = notClient && askPincode;
        var attribute;

        attributes = attributes || document.getElementById('contact-form').elements; 
        for (var i in attributes) {
            attribute = attributes[i];
            
            if (
                attribute.name === 'Contact[email]' && oldEmail
                && attribute.value !== oldEmail.value
                && askPincode
            ) {
                show = true;
                break;
            }
        }
        
        if (show && !pincodeInput.val()) {
            event.preventDefault();
            
            form.pincodePrompt().then(function (pincode) {
                pincodeInput.val(pincode);
                form.submit();
            });
            
            return false;
        }
    });
})(jQuery);
JS
); ?>

<?= \hipanel\widgets\PincodePrompt::widget();
