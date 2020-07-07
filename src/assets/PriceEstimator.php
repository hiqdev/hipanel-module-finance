<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class PriceEstimator.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceEstimator extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = __DIR__;

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
        JqueryAsset::class,
    ];
}
