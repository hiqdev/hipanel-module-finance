<?php

namespace hipanel\modules\finance\cart;

use Yii;
use hiqdev\hiart\ActiveRecord;
use hiqdev\yii2\cart\CartPositionInterface;
use hiqdev\yii2\cart\CartPositionTrait;

abstract class AbstractCartPosition extends ActiveRecord implements CartPositionInterface
{
    use CartPositionTrait;

    abstract function getId();

    /**
     * @return integer
     */
    public function getPrice()
    {
    //  return $this->model->getPrice();
        return 10;
    }

}
