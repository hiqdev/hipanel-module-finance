<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;

final class PricePerUnitWidget extends Widget
{
    public ?float $quantity;

    public ?float $sum;

    public function run(): string
    {
        $name = 'price_per_unit_' . $this->getId();
        $view = $this->view;
        $view->registerCss(<<<CSS
            .ppu-input:after, .ppu-input:before {
                position: absolute;
                top: 25px;
                font-size: x-large;
            }
            .ppu-input:before {
                content: "*";
                left: -6px;
                top: 28px;
            }
            .ppu-input:after {
                content: "=";
                right: -4px;
            }
        CSS, [__CLASS__, __METHOD__]);

        $view->registerJs(<<<"JS"
          (function () {
            $(document).on('keyup mouseup', ':input[name=$name], .form-instance :input[id$="-quantity"]', function () {
              const billForm = $(this).parents('.form-instance');
              const isPPU = this.name === '$name';
              const sumInput = billForm.find(':input[id$="-sum"]').get(0);
              const qtyInput = isPPU ? billForm.find(':input[id$="-quantity"]').get(0) : this;
              const PPUInput = isPPU ? this : billForm.find(':input[name="$name"]').get(0);
              const qty = qtyInput.value.trim();
              const amount = parseFloat(PPUInput.value.trim()) * qty;
              if (amount) {
                  sumInput.value = amount.toFixed(2);
              }
            });
          })();
JS
        );

        return sprintf('%s%s',
            Html::label(Yii::t('hipanel:finance', 'per unit'), $name, ['class' => 'control-label ppu-input']),
            Html::textInput($name, !empty($this->quantity) ? $this->sum / $this->quantity : null, ['class' => 'form-control'])
        );
    }
}
