<?php

declare(strict_types=1);

namespace hipanel\modules\finance\module\Price\Domain\Model;

final class Unit
{
    public function __construct(
        private readonly string $code,
        private readonly string $label,
    ) {
    }

    public function code(): string
    {
        return $this->code;
    }

    public function label(): string
    {
        return $this->label;
    }
}
