<?php

declare(strict_types=1);

namespace hipanel\modules\finance\assets\PnlApp;

use yii\web\AssetBundle;

class PnlCalculationAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $baseUrl = '/finance/pnl/calculation';
    public $publishOptions = ['forceCopy' => true];
    public $js = ['dist/pnl-app.js'];
}
