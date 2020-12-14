<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\widgets\HookTrait;
use yii\base\Widget;
use yii\helpers\Html;

class ResourceDetailTotalHook extends Widget
{
    use HookTrait;

    public function init(): void
    {
        parent::init();
        $this->registerJsHook('resource-total');
    }

    public function run(): string
    {
        return Html::tag('span', ResourceHelper::getResourceLoader(), ['id' => $this->getId()]);
    }
}
