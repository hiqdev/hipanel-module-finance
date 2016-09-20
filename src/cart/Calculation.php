<?php

namespace hipanel\modules\finance\cart;

class Calculation extends \hipanel\modules\finance\models\Calculation
{
    use \hipanel\base\ModelTrait;

    /**
     * @var AbstractCartPosition
     */
    public $position;

    /** {@inheritdoc} */
    public static function primaryKey()
    {
        return ['cart_position_id'];
    }

    public function synchronize()
    {
        $this->cart_position_id = $this->position->getId();
        $this->amount = $this->position->getQuantity();
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['cart_position_id'], 'safe']
        ]);
    }
}
