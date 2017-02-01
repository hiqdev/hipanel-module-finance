<?php

namespace hipanel\modules\finance\bootstrap;

use hipanel\modules\finance\widgets\PayButtonComment;
use hiqdev\yii2\merchant\widgets\PayButton;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;

class PayButtonCommentsBootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Event::on(PayButton::class, PayButton::EVENT_RENDER_COMMENT, function ($event) use ($app) {
            echo Yii::createObject(PayButtonComment::class, [$event])->run();
        });
    }
}
