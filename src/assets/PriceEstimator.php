<?php

namespace hipanel\modules\finance\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class PriceEstimator
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceEstimator extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@hipanel/modules/finance/assets/PriceEstimator';

    /**
     * @var array
     */
    public $js = [
        'js/PriceEstimator.js',
    ];

    /**
     * @var array
     */
    public $css = [
        'css/PriceEstimator.css',
    ];

    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class
    ];
}
