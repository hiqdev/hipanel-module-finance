<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\assets\PnlReport\PnlReportAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class PnlReportDataGridWidget extends Widget
{
    public array $initialState = [];

    public function run(): string
    {
        PnlReportAsset::register($this->view);
        $this->view->registerJsVar('__initial_state', $this->initialState);

        return Html::tag('div', Yii::t('hipanel:finance', 'Loading...'), ['id' => 'pnl-report-app']);
    }
}
