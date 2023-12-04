<?php

declare(strict_types=1);

namespace hipanel\modules\finance\assets\PnlApp;

use yii\web\AssetBundle;

class PnlReportAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $baseUrl = '/finance/pnl/report';
    public $publishOptions = ['forceCopy' => true];
    public $js = ['dist/pnl-app.js'];
}
