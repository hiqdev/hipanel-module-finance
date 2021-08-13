<?php

namespace hipanel\modules\finance\assets;

use dosamigos\chartjs\ChartJsAsset;
use hipanel\assets\MomentAsset;
use yii\web\AssetBundle;

class ConsumptionViewerAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $js = [
        (YII_DEBUG ? 'https://unpkg.com/vue@3.0.7' : 'https://unpkg.com/vue@3.0.7/dist/vue.global.prod.js'),
        'https://unpkg.com/lodash@4.17.20/lodash.min.js',
        'ConsumptionViewer.js',
    ];

    public $depends = [
        MomentAsset::class,
        ChartJsAsset::class,
    ];
}
