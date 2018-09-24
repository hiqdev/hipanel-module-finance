<?php

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class ColoredBalance extends Widget
{
    public $model;

    public $attribute = 'balance';

    public $nameAttribute = 'balance';

    public $url;

    /**
     * @var bool|string Whether to compare [[attribute]] with another attribute to change the display colors
     *  - boolean false - do not compare
     *  - string - name of attribute to compare with
     */
    public $compare = false;

    public $colors = [];

    public $urlCallback;

    public function getColor($type)
    {
        return $this->colors[$type] ?: $type;
    }

    public function run()
    {
        $value = $this->model->{$this->attribute};
        $color = $value === 0 ? 'primary' : 'success';

        if ($value < 0) {
            $color = 'warning';
        }

        if ($this->compare && $value < -($this->model->{$this->compare} ?: 0)) {
            $color = 'danger';
        }

        $url = $this->url;
        $txt = Yii::$app->formatter->format($value, ['currency', $this->model->currency]);
        $ops = ['class' => 'text-nowrap text-' . $this->getColor($color), 'data-pjax' => 0];
        return $url ? Html::a($txt, $url, $ops) : Html::tag('span', $txt, $ops);
    }
}
