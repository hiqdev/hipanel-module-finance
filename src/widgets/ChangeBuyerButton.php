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
    public string|array $url = ['@sale/change-buyer'];
    public string $modalName = 'change-buyer-from-modal-';
    public string $modalTitle = 'Change buyer';
    public string $scenario = 'change-buyer';
    private ?string $modalId = null;

    public function init(): void
    {
        parent::init();
        $this->modalId = $this->modalName . $this->getId();
        $url = Url::to($this->url);
        $this->view->on(View::EVENT_END_BODY, function () use ($url) {
            echo AjaxModal::widget([
                'id' => $this->modalId,
                'header' => Html::tag('h4', Yii::t('hipanel:finance', $this->modalTitle), ['class' => 'modal-title']),
                'scenario' => $this->scenario,
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
                        $.post('$url', {
                            selection: selection
                        }).done(function (data) {
                            $('#$this->modalId .modal-body').html(data);
                        });
                    }"),
                ],
            ]);
        });
    }

    public function run(): string
    {
        return Html::tag('li', Html::tag('a', Yii::t('hipanel:finance', $this->modalTitle), [
            'data' => [
                'toggle' => 'modal',
                'target' => '#' . $this->modalId,
            ],
        ]));
    }
}
