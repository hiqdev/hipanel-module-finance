<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\assets\PursesBox\PursesBoxAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class PursesBox extends Widget
{
    public array $purses = [];

    public function run(): string
    {
        PursesBoxAsset::register($this->view);
        $props = Json::encode(
            [
                'language' => Yii::$app->language,
                'contacts' => [],
                'paymentDetails' => [],
                'documentTypes' => [],
                'purses' => $this->purses,
            ],
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );

        $this->getView()->registerJs("window.PursesBox.mount(document.getElementById('$this->id'), $props);");

        return Html::tag('div', '', ['id' => $this->id]);
    }
}
