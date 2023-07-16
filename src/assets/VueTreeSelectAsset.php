<?php

declare(strict_types=1);

namespace hipanel\modules\finance\assets;

use hipanel\assets\Vue2CdnAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class VueTreeSelectAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $js = [
        'https://cdn.jsdelivr.net/npm/@riophae/vue-treeselect@^0.4.0/dist/vue-treeselect.umd.min.js',
    ];
    public $css = [
        'https://cdn.jsdelivr.net/npm/@riophae/vue-treeselect@^0.4.0/dist/vue-treeselect.min.css',
        'css/vue-treeselect-hipanel.css',
    ];

    public $depends = [
        Vue2CdnAsset::class,
        JqueryAsset::class,
    ];
}
