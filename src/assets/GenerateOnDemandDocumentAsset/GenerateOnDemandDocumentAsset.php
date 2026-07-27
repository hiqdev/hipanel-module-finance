<?php

declare(strict_types=1);

namespace hipanel\modules\finance\assets\GenerateOnDemandDocumentAsset;

use hipanel\assets\HipanelAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class GenerateOnDemandDocumentAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $js = [
        (YII_DEBUG ? 'https://unpkg.com/vue@3' : 'https://unpkg.com/vue@3/dist/vue.global.prod.js'),
        'generate-on-demand-document-app.js',
    ];
    public $depends = [
        HipanelAsset::class,
        JqueryAsset::class,
    ];
    public $publishOptions = ['only' => ['generate-on-demand-document-app.js']];
}
