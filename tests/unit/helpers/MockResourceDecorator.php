<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\helpers;

//use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;

use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorData;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;

class MockResourceDecorator implements ResourceDecoratorInterface
{
    public function getPrepaidQuantity()
    {
        // TODO: Implement getPrepaidQuantity() method.
    }

    public function getOverusePrice()
    {
        // TODO: Implement getOverusePrice() method.
    }

    public function displayTitle(): string
    {
        return 'Mock';
    }

    public function displayUnit(): ?string
    {
        // TODO: Implement displayUnit() method.
    }

    public function toUnit(): string
    {
        // TODO: Implement toUnit() method.
    }

    public function displayAmountWithUnit(): string
    {
        // TODO: Implement displayAmountWithUnit() method.
    }

    public function displayOverusePrice()
    {
        // TODO: Implement displayOverusePrice() method.
    }

    public function displayPrepaidAmount()
    {
        // TODO: Implement displayPrepaidAmount() method.
    }

    public function prepaidAmountType()
    {
        // TODO: Implement prepaidAmountType() method.
    }

    public function displayValue(): ?string
    {
        // TODO: Implement displayValue() method.
    }

    public function getData(): ResourceDecoratorData
    {
        // TODO: Implement getData() method.
    }
}
