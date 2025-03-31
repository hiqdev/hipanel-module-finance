<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\helpers;

use hipanel\modules\finance\models\decorators\ResourceDecoratorFactory;

class MockResourceDecoratorFactory extends ResourceDecoratorFactory
{
    protected static function typeMap(): array
    {
        return [
            'col1' => MockResourceDecorator::class,
            'col2' => MockResourceDecorator::class,
            'col3' => MockResourceDecorator::class,
        ];
    }
}
