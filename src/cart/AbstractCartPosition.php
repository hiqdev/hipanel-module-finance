<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use hiqdev\hiart\ActiveRecord;
use hiqdev\yii2\cart\CartPositionInterface;
use hiqdev\yii2\cart\CartPositionTrait;

abstract class AbstractCartPosition extends ActiveRecord implements CartPositionInterface
{
    use CartPositionTrait;

    abstract public function getId();

    /**
     * @return int
     */
    public function getPrice()
    {
        //  return $this->model->getPrice();
        return 10;
    }
}
