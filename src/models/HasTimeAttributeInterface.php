<?php
declare(strict_types=1);

namespace hipanel\modules\finance\models;

/**
 * @property mixed|null $time
 */
interface HasTimeAttributeInterface
{
    public function getTime(): ?string;
}
