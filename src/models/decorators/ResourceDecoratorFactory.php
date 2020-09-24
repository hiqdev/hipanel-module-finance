<?php

namespace hipanel\modules\finance\models\decorators;

use yii\base\InvalidConfigException;

class ResourceDecoratorFactory
{
    protected static function typeMap(): array
    {
        return [];
    }

    /**
     * @param $resource
     * @return ResourceDecoratorInterface
     * @throws InvalidConfigException
     */
    public static function createFromResource($resource): ResourceDecoratorInterface
    {
        $type = $resource->model_type ?? $resource->type;
        $map = static::typeMap();

        if (!isset($map[$type])) {
            throw new InvalidConfigException('No representative decoration class found for type "' . $type . '"');
        }

        return new $map[$type]($resource);
    }
}
