<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use hipanel\modules\finance\forms\CdbExportForm;

class CdbExportModalButton extends Widget
{
    public function init(): void
    {
        $this->view->on(View::EVENT_END_BODY, function () {
            $model = new CdbExportForm();

            Modal::begin([
                'id' => $this->getId(),
                'size' => Modal::SIZE_SMALL,
                'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Bulk payments CDB'), ['class' => 'modal-title']),
                'toggleButton' => false,
            ]);

            $form = ActiveForm::begin(['action' => Url::to(['@requisite/cdb-export'])]);

            echo $form->field($model, 'file')->fileInput();
            echo Html::submitButton(Yii::t('hipanel:finance', 'Convert to XML'),
                ['class' => 'btn btn-success btn-block']);

            ActiveForm::end();

            Modal::end();
        });
    }

    public function run(): string
    {
        if (!Yii::$app->user->can('bill.update')) {
            return '';
        }

        return Html::a(Yii::t('hipanel:finance', 'Bulk payments CDB'), '#', [
            'class' => 'btn btn-sm btn-success',
            'data' => ['toggle' => 'modal', 'target' => '#' . $this->getId()],
        ]);
    }
}
