<?php declare(strict_types=1);

namespace hipanel\modules\finance\assets\FinanceDocumentsBox;

use yii\web\AssetBundle;
use yii\web\View;

class FinanceDocumentsBoxAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/build';
    public $jsOptions = [
        'defer' => true,
        'position' => View::POS_END,
    ];
    public $js = ['finance-documents-box.js'];
    public $css = ['finance-documents-box.css'];

    public function init(): void
    {
        parent::init();
        $file = $this->sourcePath . '/finance-documents-box.js';
        if (file_exists($file)) {
            $this->jsOptions['appendTimestamp'] = true;
        }
    }
}
