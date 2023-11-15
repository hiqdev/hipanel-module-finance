<?php

/**
 * @var array $initialState
 */

use hipanel\modules\finance\widgets\PnlReportDataGridWidget;

$this->title = Yii::t('hipanel:finance', 'P&L Report');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= PnlReportDataGridWidget::widget(['initialState' => $initialState]) ?>
