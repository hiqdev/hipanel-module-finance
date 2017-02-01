<?php

namespace hipanel\modules\finance\behaviors;

use hipanel\modules\finance\widgets\PayButtonComment;
use hiqdev\yii2\merchant\widgets\PayButton;
use Yii;
use yii\base\Behavior;

class PayButtonCommentBehavior extends Behavior
{
    public function events()
    {
        return [
            PayButton::EVENT_RENDER_COMMENT => 'handleCommentBehavior'
        ];
    }

    public function handleCommentBehavior($event)
    {
        echo Yii::createObject(PayButtonComment::class, [$event])->run();
    }
}
