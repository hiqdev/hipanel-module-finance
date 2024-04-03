<?php

namespace hipanel\modules\finance\assets;

use dosamigos\chartjs\ChartJsAsset;
use hipanel\assets\HipanelAsset;
use hipanel\assets\MomentAsset;
use hipanel\assets\Vue2CdnAsset;
use yii\web\AssetBundle;

class ConsumptionViewerAsset extends AssetBundle
{
    public bool $showCharts = true;
    public $sourcePath = __DIR__;
    public $js = [
        'https://unpkg.com/lodash@4.17.20/lodash.min.js',
        'ConsumptionViewer.js',
    ];
    public $publishOptions = ['only' => ['ConsumptionViewer.js']];

    public function init()
    {
        $depends = [
            MomentAsset::class,
            HipanelAsset::class,
            Vue2CdnAsset::class,
        ];
        if ($this->showCharts) {
            $depends[] = ChartJsAsset::class;
        }
        $this->depends = $depends;
        parent::init();
    }
}
