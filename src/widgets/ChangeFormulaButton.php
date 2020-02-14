<?php

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;

class ChangeFormulaButton extends Widget
{
    public const MOD_ADD = 'add';

    public const MOD_REPLACE = 'replace';

    /**
     * @var string the modal size. Can be [[MOD_REPLACE]] or [[MOD_ADD]]
     */
    public $mod;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $modalId;

    public function init()
    {
        if (empty($this->mod)) {
            throw new InvalidConfigException('`$mod` must be set');
        }
        $modalId = mt_rand();
        $this->modalId = $modalId;
        $operation = [
            self::MOD_ADD => Yii::t('hipanel:finance', 'Add formula'),
            self::MOD_REPLACE => Yii::t('hipanel:finance', 'Replace formulas'),
        ];
        $this->label = $operation[$this->mod];
        $this->view->registerCss('
        .modal-body .ace_editor { min-height: 34px; padding: 0; }
        .box.box-solid > .box-header .btn:hover, .box.box-solid > .box-header a:hover {
            background-color: #008d4c;
        }
        ');
        $this->view->registerJs($this->getJsExpression(), View::POS_END);
        $this->view->on(View::EVENT_END_BODY, function () use ($modalId) {
            Modal::begin([
                'id' => $this->modalId,
                'header' => Html::tag('h4', $this->label, ['class' => 'modal-title']),
                'toggleButton' => false,
                'footer' => Html::button($this->label, [
                    'class' => 'btn btn-success',
                    'data-dismiss' => 'modal',
                ]),
            ]);
            echo Html::textarea('modal-formula-input', null, ['id' => $modalId . '-modal-formula-input', 'class' => 'form-control formula-input']);
            Modal::end();
        });

        parent::init();
    }

    /**
     * @return string
     */
    public function run(): string
    {
        return Html::button($this->label, [
            'class' => 'btn btn-success btn-sm',
            'data' => [
                'toggle' => 'modal',
                'target' => '#' . $this->modalId,
            ],
        ]);
    }

    private function getJsExpression(): string
    {
        $jsExpression = [
            self::MOD_ADD => new JsExpression(<<<"JS"
$('#{$this->modalId}.modal button').on('click', () => {
    const t = $('.modal.in .formula-input').first().data('ace-editor').session;
    $(".form-group .formula-input").each((idx, elem) => {
        const e = $(elem).data("ace-editor").session;
        const n = "\\n" + t.getValue();
        e.insert({row: e.getLength(), column: 0}, n);
    });
    t.setValue('');
});
JS
            ),
            self::MOD_REPLACE => new JsExpression(<<<"JS"
$('#{$this->modalId}.modal button').on('click', () => {
    const t = $('.modal.in .formula-input').first().data('ace-editor').session;
    $(".form-group .formula-input").each((idx, elem) => {
        $(elem).data("ace-editor").session.setValue(t.getValue());
    });
    t.setValue('');
});
JS
            ),
        ];

        return $jsExpression[$this->mod];
    }
}
