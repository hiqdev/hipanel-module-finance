<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;

class PriceInput extends Widget
{
    public $basePrice = 0;
    public $originalPrice = 0;
    public $activeField;

    public function run()
    {
        $this->registerClientScript();

        return $this->render('PriceInput', [
            'basePrice' => $this->basePrice,
            'originalPrice' => $this->originalPrice,
            'activeField' => $this->activeField,
        ]);
    }

    public function registerClientScript()
    {
        $this->view->registerJs(<<<'JS'
            $('.price-input').on('change mouseup', function () {
                var price = parseFloat($(this).val());
                if (isNaN(price)) return false;
                var base = $(this).closest('td').find('.base-price'),
                    basePrice = parseFloat(base.attr('data-original-price')),
                    delta = price - basePrice;

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
