<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\unit\controllers;

use hipanel\modules\finance\controllers\CartController;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-04-27 at 13:36:04.
 */
class CartControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CartController
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new CartController('bill', null);
    }

    protected function tearDown()
    {
    }

    public function testActions()
    {
        $this->assertInternalType('array', $this->object->actions());
    }
}
