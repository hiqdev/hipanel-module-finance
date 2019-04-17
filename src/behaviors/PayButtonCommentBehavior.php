<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

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
            PayButton::EVENT_RENDER_COMMENT => 'handleCommentBehavior',
        ];
    }

    public function handleCommentBehavior($event)
    {
        echo Yii::createObject(PayButtonComment::class, [$event])->run();
    }
}
