<?php

declare(strict_types=1);

namespace hipanel\modules\finance\assets;

use yii\web\AssetBundle;

class VueTreeselectAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $js = [
        'https://cdn.jsdelivr.net/npm/vue@^2',
        'https://cdn.jsdelivr.net/npm/@riophae/vue-treeselect@^0.4.0/dist/vue-treeselect.umd.min.js',
    ];
    public $css = [
        'https://cdn.jsdelivr.net/npm/@riophae/vue-treeselect@^0.4.0/dist/vue-treeselect.min.css',
        'css/vue-treeselect-hipanel.css',
    ];

    public static function register($view)
    {
        $view->registerJs("Vue.component('treeselect', VueTreeselect.Treeselect)");

        return parent::register($view);
    }
}
