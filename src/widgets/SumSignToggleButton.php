<?php

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

class SumSignToggleButton extends Widget
{
    public array $options = [];

    public function run(): string
    {
        $this->view->registerJs(<<<"JS"
            (function() {
              $('.btn-toggle-sign').click(function () {
                const inputs = $(this).parents('.bill-item').find(':input[id$="-sum"]');
                inputs.each(function () {
                  this.value = this.value.startsWith('-') ? this.value.substring(1) : '-' + this.value;
                });
              });
            })();
JS
            , View::POS_READY, __CLASS__);
        $options = array_merge([
            'class' => 'btn btn-default btn-sm',
        ], $this->options);
        $options['class'] .= ' btn-toggle-sign';
        $icon = Html::tag('i', '', ['class' => 'fa fa-exchange fa-fw']);

        return Html::button(implode(" ", [$icon, Yii::t('hipanel:finance', 'Toggle sign')]), $options);
    }
}
