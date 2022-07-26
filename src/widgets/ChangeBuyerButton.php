<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\widgets\AjaxModal;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

class ChangeBuyerButton extends Widget
{
    private ?string $modalId = null;

    public function init(): void
    {
        parent::init();
        $this->modalId = 'change-buyer-from-modal-' . $this->getId();
        $url = Url::to(['@sale/change-buyer']);
        $this->view->on(View::EVENT_END_BODY, function () use ($url) {
            echo AjaxModal::widget([
                'id' => $this->modalId,
                'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Change buyer'), ['class' => 'modal-title']),
                'scenario' => 'change-buyer',
                'actionUrl' => $url,
                'toggleButton' => false,
                'size' => Modal::SIZE_LARGE,
                'clientEvents' => [
                    'show.bs.modal' => new JsExpression("function(evt) {
                        if (evt.namespace !== 'bs.modal') return true;
                        const selection = [];
                        $(':checked[name^=\"selection\"]').not('.select-on-check-all').each(function (index) {
                            selection.push(this.value);
                        });
                        $.post('{$url}', {
                            selection: selection
                        }).done(function (data) {
                            $('#{$this->modalId} .modal-body').html(data);
                        });
                    }"),
                ],
            ]);
        });
    }

    public function run(): string
    {
        return Html::button(Yii::t('hipanel:finance', 'Change buyer'), [
            'class' => 'btn btn-default btn-sm',
            'data' => [
                'toggle' => 'modal',
                'target' => '#' . $this->modalId,
            ],
        ]);
    }
}
