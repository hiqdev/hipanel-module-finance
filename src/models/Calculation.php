<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\modules\finance\cart\AbstractCartPosition;
use Yii;

/**
 * Class Calculation.
 */
class Calculation extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /**
     * @var AbstractCartPosition
     */
    public $position;

    /** {@inheritdoc} */
    public static function index()
    {
        return 'actions';
    }

    /** {@inheritdoc} */
    public static function type()
    {
        return 'action';
    }

    /** {@inheritdoc} */
    public static function primaryKey()
    {
        return ['cart_position_id'];
    }

    /** {@inheritdoc} */
    public function init()
    {
        if (Yii::$app->user->isGuest) {
            $this->seller = Yii::$app->user->identity->seller;
        } else {
            $this->client = Yii::$app->user->identity->username;
        }

        $this->synchronize();
    }

    /**
     * Synchronises the model to represent actual state of [[position]]
     * The method must update values, that affects the calculation and
     * can be changed in cart without position re-adding.
     * For example: quantity.
     */
    public function synchronize()
    {
        $this->cart_position_id = $this->position->getId();
        $this->amount = $this->position->getQuantity();
    }

    /** {@inheritdoc} */
    public function rules()
    {
        return [
            [['cart_position_id', 'object', 'seller', 'client', 'type', 'currency', 'item'], 'safe'],
            [['amount'], 'number'],
        ];
    }
}
