<?php

namespace hipanel\modules\finance\models;

use Yii;

class Calculation extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /** @inheritdoc */
    public static function index()
    {
        return 'actions';
    }

    /** @inheritdoc */
    public static function type()
    {
        return 'action';
    }

    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['cart_position_id'];
    }

    /** @inheritdoc */
    public function init()
    {
        if (Yii::$app->user->isGuest) {
            $this->seller = Yii::$app->user->seller;
        } else {
            $this->client = Yii::$app->user->identity->username;
        }
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['cart_position_id', 'object', 'seller', 'client', 'type', 'currency', 'item'], 'safe'],
            [['amount'], 'number'],
        ];
    }
}