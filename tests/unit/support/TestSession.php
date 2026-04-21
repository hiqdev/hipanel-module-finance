<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\support;

use yii\base\Component;

final class TestSession extends Component
{
    public array $flashes = [];

    public function addFlash($type, $message, $removeAfterAccess = true): void
    {
        $this->flashes[$type][] = $message;
    }

    public function setFlash($type, $message, $removeAfterAccess = true): void
    {
        $this->flashes[$type] = [$message];
    }
}
