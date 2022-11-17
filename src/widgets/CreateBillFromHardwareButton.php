<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\assets\VueTreeselectAsset;
use hipanel\modules\finance\models\Plan;
use hipanel\widgets\AjaxModal;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

class CreateBillFromHardwareButton extends Widget
{
    public Plan $model;

    private ?string $modalId;

    public function init(): void
    {
        parent::init();
        VueTreeselectAsset::register($this->view);
        $this->modalId = 'create-bill-from-prices-modal-' . $this->getId();
        $url = Url::to(['@bill/create-from-prices']);
        $this->view->on(View::EVENT_END_BODY, function () use ($url) {
            echo AjaxModal::widget([
                'id' => $this->modalId,
                'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Prepare to bill creation'), ['class' => 'modal-title']),
                'scenario' => 'create-from-prices',
                'actionUrl' => $url,
                'toggleButton' => false,
                'clientEvents' => [
                    'show.bs.modal' => new JsExpression("function(evt) {
                        if (evt.namespace !== 'bs.modal') return true;
                        const selection = [];
                        $(':checked[name^=\"selection\"]').each(function (index) {
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
        return Html::button(Yii::t('hipanel:finance', 'Create bill'), [
            'class' => 'btn btn-success btn-sm',
            'data' => [
                'toggle' => 'modal',
                'target' => '#' . $this->modalId,
            ],
        ]);
    }
}
