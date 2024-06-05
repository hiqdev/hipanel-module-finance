<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit;

use hiqdev\yii\compat\yii;
use Psr\Container\ContainerInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @return \yii\di\Container|ContainerInterface
     */
    public function di()
    {
        return yii::getContainer();
    }
}