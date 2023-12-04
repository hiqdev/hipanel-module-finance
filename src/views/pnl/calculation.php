<?php

use hipanel\modules\finance\assets\PnlApp\PnlCalculationAsset;
use yii\helpers\Html;

/** @var array $initialState */

$this->title = Yii::t('hipanel:finance', 'P&L Calculation');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsVar('__initial_state', $initialState);
PnlCalculationAsset::register($this);

echo Html::tag('div', Yii::t('hipanel:finance', 'Loading...'), ['id' => 'pnl-calculation-app']);
