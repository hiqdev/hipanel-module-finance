<?php

namespace hipanel\modules\finance\widgets;

use hipanel\widgets\AjaxModalWithTemplatedButton;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

class AddExtraPricesButton extends Widget
{
    public int $plan_id;

    public string $type;

    public array $toggleButton = [];

    public function run(): string
    {
        $id = 'extra-prices-modal';
        $this->view->registerJs(<<<JS
            const _selection_grabber = function (e) {
                var selection = [];
                $('#prices-form .price-item').each(function() {
                    const row = $(this);
                    const object_id = row.find('input[id$=\"object_id\"]').val();
                    if (object_id) {
                        const name = row.find('input[id$=\"object\"]').val();
                        const price = row.find('input[id$=\"-price\"]').val();
                        const type = row.find('input[id$=\"-type\"]').val();
                        const note = row.find('input[id$=\"-note\"].form-control').val();
                        const eur = row.find('input[id$=\"[EUR]\"]').val();
                        selection.push({
                            object_id: object_id,
                            name: name,
                            price_type: type,
                            price: price,
                            note: note,
                            eur: parseInt(eur) * 100
                        });
                    }
                });

                return selection;
            }
JS
            , View::POS_HEAD);

        return AjaxModalWithTemplatedButton::widget([
            'ajaxModalOptions' => [
                'id' => $id,
                'bulkPage' => false,
                'header' => Html::tag('h4', $this->getLabel(), ['class' => 'modal-title']),
                'scenario' => 'default',
                'actionUrl' => $this->getUrl(),
                'handleSubmit' => $this->getUrl(),
                'toggleButton' => array_merge([
                    'tag' => 'a',
                    'label' => $this->getLabel(),
                    'class' => 'btn btn-sm btn-success',
                    'style' => 'cursor: pointer;',
                ], $this->toggleButton),
                'clientEvents' => [
                    'show.bs.modal' => new JsExpression("function (e) {
                        if (e.namespace !== 'bs.modal') return true;
                        const selection = _selection_grabber();
                        $.post('{$this->getUrl()}', {selection: selection}).done(function (html) {
                            $('#{$id} .modal-body').html(html);
                        });
                    }"),
                ],
            ],
            'toggleButtonTemplate' => '{toggleButton}',
        ]);
    }

    private function getUrl(): string
    {
        return Url::toRoute(['@price/add-extra-prices', 'plan_id' => $this->plan_id, 'type' => $this->type]);
    }

    private function getLabel(): string
    {
        switch ($this->type) {
            case 'calculator_public_cloud':
                return Yii::t('hipanel:finance', 'Add config prices');
            case 'calculator_private_cloud':
                return Yii::t('hipanel:finance', 'Add model prices');
            default:
                return Yii::t('hipanel:finance', 'Add extra prices');
        }
    }
}
