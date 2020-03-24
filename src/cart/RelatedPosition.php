<?php

namespace hipanel\modules\finance\cart;

use hipanel\modules\finance\logic\Calculator;
use hipanel\modules\finance\models\CalculableModelInterface;
use hiqdev\yii2\cart\CartPositionInterface;
use hiqdev\yii2\cart\Module;
use hiqdev\yii2\cart\ShoppingCart;

abstract class RelatedPosition implements RelatedPositionInterface
{
    /** @var CartPositionInterface */
    public $mainPosition;

    /** @var ShoppingCart */
    public $cart;

    /** @var CalculableModelInterface */
    public $relatedPosition;

    public function __construct(CartPositionInterface $mainPosition)
    {
        $this->cart = Module::getInstance()->getCart();
        $this->mainPosition = $mainPosition;
        $position = $this->createRelatedPosition();
        $this->relatedPosition = $this->calculate($position);
    }

    /** @inheritDoc */
    public function render(): string
    {
        return $this->getWidget()->run();
    }

    public function calculate(CalculableModelInterface $position): CalculableModelInterface
    {
        $calculator = new Calculator([$position]);
        $calculationId = $position->getCalculationModel()->calculation_id;
        $calculation = $calculator->getCalculation($calculationId);
        $value = $calculation->forCurrency($this->cart->getCurrency());
        $position->setPrice($value->price);
        $position->setValue($value->value);
        $position->setCurrency($value->currency);

        return $position;
    }

}
