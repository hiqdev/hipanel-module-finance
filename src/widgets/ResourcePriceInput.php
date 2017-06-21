<?php

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;

class ResourcePriceInput extends Widget
{
    public $basePrice = 0;
    public $activeField;

    public function run()
    {
        $this->registerClientScript();

        return $this->render('ResourcePriceInput', [
            'basePrice' => $this->basePrice,
            'activeField' => $this->activeField,
        ]);
    }

    public function registerClientScript()
    {
        $this->view->registerJs(<<<'JS'
            $('.price-input').on('change mouseup', function () {
                var price = parseFloat($(this).val());
                if (isNaN(price)) return false;
                
                var base = $(this).closest('td').find('.base-price');
                var basePrice = parseFloat(base.attr('data-original-price'));
                var delta = price - basePrice;
                
                if (delta <= -basePrice) {
                    $(this).val('0.01').trigger('change');
                    return false;
                }
                
                base.removeClass('text-success text-danger');
                
                base.text(delta.toFixed(2)).addClass(delta >= 0 ? 'text-success' : 'text-danger');
            });
        
            $('.price-input').trigger('change');
JS
        );

        $this->view->registerCss('
            .base-price { font-weight: bold; }
            .form-group.form-group-sm { margin-bottom: 0; }
        ');
    }
}
