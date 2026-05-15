<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\assets\FinanceDocumentsBox\FinanceDocumentsBoxAsset;
use hipanel\modules\finance\widgets\FinanceDocumentsBox\FinanceDocumentsDataSource;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\Application;

final class FinanceDocumentsBox extends Widget
{
    public FinanceDocumentsDataSource $dataSource;
    private Application $app;

    public function init(): void
    {
        $this->app = Yii::$app;
    }

    public function run(): string
    {
        if (!$this->app->user->can('bill.read') || !$this->dataSource->hasDocuments()) {
            return '';
        }

        FinanceDocumentsBoxAsset::register($this->view);

        $mfn = $this->dataSource->getMountFunctionName();
        $props = $this->dataSource->buildJsProps();
        $this->view->registerJs(
            "window.FinanceDocumentsBox.$mfn(document.getElementById('$this->id'), $props);"
        );

        return Html::tag('div', '', ['id' => $this->id]);
    }
}
