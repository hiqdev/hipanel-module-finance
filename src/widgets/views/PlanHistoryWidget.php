<?php


use yii\bootstrap\Collapse;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var array $collapseItems
 */

$this->registerCss(<<<CSS
.panel-group .panel + .panel {
    margin-top: 0;
}
.panel-group {
    margin-bottom: 0;
}
.panel-body {
    padding: 0;
}
CSS
);

?>

<div class="box box-solid">
    <div class="box-body no-padding">
        <div class="mailbox-controls">
            <?= HTML::tag('h4', '&nbsp;' . Yii::t('hipanel', 'Tariff history')) ?>
        </div>
        <?= Collapse::widget($collapseItems) ?>
    </div>
</div>
