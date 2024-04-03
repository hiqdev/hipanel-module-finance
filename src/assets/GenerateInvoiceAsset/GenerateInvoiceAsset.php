<?php

namespace hipanel\modules\finance\assets\GenerateInvoiceAsset;

use hipanel\assets\BootstrapDatetimepickerAsset;
use hipanel\assets\HipanelAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\View;

class GenerateInvoiceAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $js = [
        (YII_DEBUG ? 'https://unpkg.com/vue@3' : 'https://unpkg.com/vue@3/dist/vue.global.prod.js'),
        'generate-invoice-app.js'
    ];
    public $css = [
        'generate-invoice-app.css'
    ];
    public $depends = [
        HipanelAsset::class,
        JqueryAsset::class,
        BootstrapDatetimepickerAsset::class
    ];
    public $publishOptions = ['only' => ['generate-invoice-app.css', 'generate-invoice-app.js']];
}
