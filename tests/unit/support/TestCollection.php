<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\support;

use hiqdev\hiart\Collection;

final class TestCollection extends Collection
{
    public bool $saveResult = false;
    public ?\Throwable $saveThrowable = null;
    public int $loadCalls = 0;
    public int $saveCalls = 0;

    public function load($data = null)
    {
        ++$this->loadCalls;

        return $this;
    }

    public function save($runValidation = true, $attributes = null, $options = [])
    {
        ++$this->saveCalls;

        if ($this->saveThrowable !== null) {
            throw $this->saveThrowable;
        }

        return $this->saveResult;
    }
}
