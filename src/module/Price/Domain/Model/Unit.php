<?php

declare(strict_types=1);

namespace hipanel\modules\finance\module\Price\Domain\Model;

final class Unit
{
    public function __construct(
        public readonly string $code,
        public readonly string $label,
    ) {
    }
}
