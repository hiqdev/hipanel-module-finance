<?php declare(strict_types=1);

namespace hipanel\modules\finance\assets\PursesBox;

use yii\web\AssetBundle;
use yii\web\View;

class PursesBoxAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/build';
    public $jsOptions = [
        'defer' => true,
        'position' => View::POS_END,
    ];
    public $js = ['purses-box.js'];
    public $css = ['purses-box.css'];

    public function init(): void
    {
        parent::init();
        $file = $this->sourcePath . '/purses-box.js';
        if (file_exists($file)) {
            $this->jsOptions['appendTimestamp'] = true;
        }
    }
}
