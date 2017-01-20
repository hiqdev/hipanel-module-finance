<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\stubs;

use hipanel\modules\finance\models\decorators\AbstractResourceDecorator;
use hipanel\modules\finance\models\Tariff;
use yii\base\InvalidConfigException;
use yii\base\Model;

abstract class AbstractResourceStub extends Model
{
    /**
     * @var Tariff
     */
    public $tariff;

    /**
     * @var string
     */
    public $type;

    /**
     * @var AbstractResourceDecorator
     */
    protected $decorator;

    /**
     * @throws InvalidConfigException
     * @return AbstractResourceDecorator
     */
    public function decorator()
    {
        throw new InvalidConfigException('Method "decorator" is not available for class Resource');
    }
}
