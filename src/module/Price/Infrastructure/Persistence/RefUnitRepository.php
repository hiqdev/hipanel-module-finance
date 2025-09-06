<?php

declare(strict_types=1);

namespace hipanel\modules\finance\module\Price\Infrastructure\Persistence;

use hipanel\models\Ref;
use hipanel\modules\finance\module\Price\Domain\Collection\UnitCollection;
use hipanel\modules\finance\module\Price\Domain\Model\Unit;

class RefUnitRepository
{
    public function findAll(): UnitCollection
    {
        $units = Ref::getList('type,unit', 'hipanel.finance.units', [
            'with_recursive' => 1,
            'select' => 'oname_label',
            'mapOptions' => ['from' => 'oname'],
        ]);

        return new UnitCollection(array_map(
            fn($code, $label) => new Unit($code, $label),
            array_keys($units),
            $units
        ));
    }
}
