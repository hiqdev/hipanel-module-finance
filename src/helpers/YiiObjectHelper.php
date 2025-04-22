<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use hipanel\base\Model;
use Yii;

class YiiObjectHelper
{
    public static function createObject(string $className, array $params = []): Model
    {
        return Yii::createObject(array_merge(['class' => $className], $params));
    }
}
