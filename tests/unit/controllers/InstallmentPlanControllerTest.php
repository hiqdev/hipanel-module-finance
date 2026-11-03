<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\unit\controllers;

use hipanel\modules\finance\controllers\InstallmentPlanController;

class InstallmentPlanControllerTest extends \PHPUnit\Framework\TestCase
{
    protected InstallmentPlanController $object;

    protected function setUp(): void
    {
        $this->object = new InstallmentPlanController('installment-plan', null);
    }

    public function testActions(): void
    {
        $this->assertIsArray($this->object->actions());
    }

    public function testBehaviors(): void
    {
        $this->assertIsArray($this->object->behaviors());
    }
}
