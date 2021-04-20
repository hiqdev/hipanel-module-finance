<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\forms\BillImportFromFileForm;
use hipanel\modules\finance\widgets\combo\BillFileImportRequisiteCombo;
use Yii;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

class BillImportDropdownButton extends Widget
{
    public function init(): void
    {
        $this->view->on(View::EVENT_END_BODY, function () {
            $model = new BillImportFromFileForm();
            Modal::begin([
                'id' => $this->getId(),
                'size' => Modal::SIZE_SMALL,
                'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Create bills from file'), ['class' => 'modal-title']),
                'toggleButton' => false,
            ]);

            $form = ActiveForm::begin([
                'action' => Url::to(['@bill/import-from-file']),
            ]);

            echo $form->field($model, 'requisite_id')->widget(BillFileImportRequisiteCombo::class);
            echo $form->field($model, 'file')->fileInput();
            echo Html::submitButton(Yii::t('hipanel:finance', 'Create bills'), ['class' => 'btn btn-success btn-block']);

            ActiveForm::end();

            Modal::end();
        });
    }

    public function run(): string
    {
        if (!Yii::$app->user->can('bill.import')) {
            return '';
        }

        return sprintf(
            '<div class="dropdown">
                <a data-toggle="dropdown" class="btn btn-sm btn-default dropdown-toggle clickable">
                     %s <b class="caret"></b>
                </a>
                %s
            </div>',
            Yii::t('hipanel:finance', 'Import payments'),
            Dropdown::widget([
                'items' => [
                    [
                        'label' => '<i class="fa fa-list"></i> ' . Yii::t('hipanel:finance', 'Import payments'),
                        'url' => ['@bill/import'],
                        'encode' => false,
                    ],
                    [
                        'label' => '<i class="fa fa-file-text-o"></i> ' . Yii::t('hipanel:finance', 'Import from a file'),
                        'url' => '#',
                        'linkOptions' => [
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#' . $this->getId(),
                            ],
                        ],
                        'encode' => false,
                    ],
                ],
            ])
        );
    }
}
