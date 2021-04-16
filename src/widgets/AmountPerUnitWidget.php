<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;

final class AmountPerUnitWidget extends Widget
{
    public function run(): string
    {
        $name = 'amount_per_unit_' . $this->getId();
        $view = $this->view;
        $view->registerCss(<<<CSS
            .apu-input:after, .apu-input:before {
                position: absolute;
                top: 25px;
                font-size: x-large;
            }
            .apu-input:before {
                content: "*";
                left: -6px;
                top: 28px;
            }
            .apu-input:after {
                content: "=";
                right: -4px;
            }
        CSS
        );

        $view->registerJs(<<<"JS"
          (function () {
            $(document).on('keyup mouseup', ':input[name=$name], .form-instance :input[id$="-quantity"]', function () {
              const billForm = $(this).parents('.form-instance');
              const isAPU = this.name === '$name';
              const sumInput = billForm.find(':input[id$="-sum"]').get(0);
              const qtyInput = isAPU ? billForm.find(':input[id$="-quantity"]').get(0) : this;
              const APUInput = isAPU ? this : billForm.find(':input[name="$name"]').get(0);
              const qty = qtyInput.value.trim();
              const amount = parseFloat(APUInput.value.trim()) * qty;
              if (amount) {
                  sumInput.value = amount.toFixed(2);
              }
            });
          })();
JS
        );

        return sprintf('%s%s',
            Html::label(Yii::t('hipanel:finance', 'per unit'), $name, ['class' => 'control-label apu-input']),
            Html::input('number', $name, null, ['class' => 'form-control'])
        );
    }
}
