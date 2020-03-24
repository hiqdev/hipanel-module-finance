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
        $this->relatedPosition = $this->createRelatedPosition();
        $currentPositions = $this->cart->getPositions();
        if (isset($currentPositions[$this->relatedPosition->getId()])) {
            $relatedPosition = $currentPositions[$this->relatedPosition->getId()];
            $relatedPosition->setQuantity($this->mainPosition->getQuantity());
            $this->relatedPosition = $relatedPosition;
        } else {
            $this->calculate();
        }
    }

    /** @inheritDoc */
    public function render(): string
    {
        return $this->getWidget()->run();
    }

    protected function calculate(): void
    {
        $position = $this->relatedPosition;
        $calculator = new Calculator([$position]);
        $calculationId = $position->getId();
        $calculation = $calculator->getCalculation($calculationId);
        $value = $calculation->forCurrency($this->cart->getCurrency());
        $position->setPrice($value->price);
        $position->setValue($value->value);
        $position->setCurrency($value->currency);
        $this->relatedPosition = $position;
    }
}
