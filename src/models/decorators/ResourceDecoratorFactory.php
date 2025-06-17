<?php declare(strict_types=1);

namespace hipanel\modules\finance\models\decorators;

use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\TariffPriceTypeSpecification;
use hipanel\modules\finance\models\Resource;
use hipanel\modules\finance\models\stubs\AbstractResourceStub;
use hiqdev\billing\registry\behavior\ResourceDecoratorBehavior;
use hiqdev\billing\registry\behavior\ResourceDecoratorBehaviorNotFoundException;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorBehaviorSearch;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorData;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use Yii;
use yii\base\InvalidConfigException;

class ResourceDecoratorFactory
{
    /**
     * @param Resource|AbstractResourceStub $resource
     * @return ResourceDecoratorInterface
     * @throws InvalidConfigException
     */
    public static function createFromResource(Resource|AbstractResourceStub $resource): ResourceDecoratorInterface
    {
        $type = $resource->model_type ?? $resource->type;
        $resourceDecoratorData = self::createResourceDecoratorData($resource);
        $registry = Yii::createObject(BillingRegistryServiceInterface::class);
        $tariffResourceHelper = new TariffPriceTypeSpecification();

        $resourceDecorator = $tariffResourceHelper->getResourceDecorator($resourceDecoratorData, $type);
        if ($resourceDecorator) {
            return $resourceDecorator;
        }

        try {
            $behavior = self::findResourceDecoratorBehavior($registry, $type);

            return $behavior->createDecorator($resourceDecoratorData);
        } catch (ResourceDecoratorBehaviorNotFoundException) {
            throw new InvalidConfigException('No representative decoration class found for type "' . $type . '"');
        }
    }

    private static function createResourceDecoratorData(Resource|AbstractResourceStub $resource): ResourceDecoratorData
    {
        $part = $resource->part;

        return new ResourceDecoratorData(
            $resource->quantity,
            $resource->price,
            $resource->unit,
            $resource->currency,
            $resource->type,
            $part ? $part->partno : '',
        );
    }

    private static function findResourceDecoratorBehavior(
        BillingRegistryServiceInterface $registry,
        string $type
    ): ResourceDecoratorBehavior {
        return (new ResourceDecoratorBehaviorSearch())->find($registry, $type);
    }
}
