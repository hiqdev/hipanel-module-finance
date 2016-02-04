<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use hipanel\modules\finance\models\Calculation;
use hiqdev\hiart\ErrorResponseException;
use hiqdev\yii2\cart\ShoppingCart;
use Yii;
use yii\base\Object;
use yii\web\UnprocessableEntityHttpException;
use yz\shoppingcart\CartActionEvent;

class CartCalculator extends Object
{
    /**
     * @var ShoppingCart
     */
    public $cart;

    /**
     * @var CartActionEvent
     */
    public $event;

    /**
     * @var AbstractCartPosition
     */
    public $position;

    /**
     * Creates the instance of the object and runs the calculation.
     *
     * @param CartActionEvent $event The event
     * @void
     */
    public static function execute($event)
    {
        $calculator = new static(['event' => $event]);
        $calculator->run();
    }

    /** {@inheritdoc} */
    public function init()
    {
        $this->cart = $this->event->sender;
        $this->position = $this->event->position;
    }

    /**
     * Runs the calculation.
     * Normally, the method should call [[calculateValue]].
     * @void
     */
    public function run()
    {
        $this->calculateValue($this->getCalculationModels());
    }

    /**
     * The method sends the request to the billing and update positions.
     *
     * @param Calculation[] $models
     * @see updatePositions
     * @throws UnprocessableEntityHttpException
     */
    protected function calculateValue($models)
    {
        $data = [];

        foreach ($models as $model) {
            $data[$model->getPrimaryKey()] = $model->getAttributes();
        }

        $response = $this->sendRequest($data);
        $this->updatePositions($response);
    }

    /**
     * Sends request to the billing API.
     *
     * @param array $data cart items structured properly for the billing API
     * @throws UnprocessableEntityHttpException
     * @return array
     */
    private function sendRequest($data)
    {
        if (empty($data)) {
            return [];
        }

        try {
            $result = Calculation::perform('CalcValue', $data, true);
        } catch (ErrorResponseException $e) {
            $result = $e->errorInfo['response'];
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException('Failed to calculate cart value', 0, $e);
        }

        return $result;
    }

    /**
     * Updates positions with the value from $data.
     *
     * @param array $data
     */
    private function updatePositions($data)
    {
        foreach ($this->cart->positions as $position) {
            $id = $position->id;
            if (isset($data[$id])) {
                $value = reset($data[$id]['value']); // data is wrapped with currency. todo: dynamic currencies
                $position->setPrice($value['price']);
                $position->setValue($value['value']);
            } else {
                Yii::error('Cart position was removed from the cart because of failed value calculation. Normally this should never happen.', 'hipanel.cart');
                $this->cart->removeById($position->id);
                break;
            }
        }
    }

    /**
     * Collects the calculation models form the cart positions.
     *
     * @return Calculation[]
     */
    protected function getCalculationModels()
    {
        $models = [];

        foreach ($this->cart->positions as $position) {
            $models[$position->id] = $position->getCalculationModel();
        }

        return $models;
    }
}
