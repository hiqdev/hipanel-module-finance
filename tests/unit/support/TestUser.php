<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\support;

use yii\base\Component;

final class TestUser extends Component
{
    public array $canMap = [];

    public function can($permissionName, $params = [], $allowCaching = true): bool
    {
        return $this->canMap[$permissionName] ?? false;
    }
}
