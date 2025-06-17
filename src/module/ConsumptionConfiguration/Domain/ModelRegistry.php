<?php declare(strict_types=1);

namespace hipanel\modules\finance\module\ConsumptionConfiguration\Domain;

use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\models\TargetResource;

class ModelRegistry
{
    public function getDefaultModels(): array
    {
        return [
            Target::class,
            TargetResource::class,
        ];
    }
}
