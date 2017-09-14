<?php

namespace hipanel\modules\finance\cart;

use hiqdev\hiart\ResponseErrorException;
use hiqdev\yii2\cart\ShoppingCart;
use Yii;
use yii\base\InvalidParamException;

/**
 * Class BatchPurchaseStrategy purchases positions in batch
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class BatchPurchaseStrategy implements PurchaseStrategyInterface
{
    use PurchaseResultTrait;

    /**
     * @var AbstractCartPosition[]
     */
    protected $positions = [];

    /**
     * @var ShoppingCart
     */
    protected $cart;

    /**
     * @var AbstractPurchase[]
     */
    protected $purchases;

    /**
     * BatchPurchaseStrategy constructor.
     *
     * @param ShoppingCart $cart
     */
    public function __construct(ShoppingCart $cart)
    {
        $this->cart = $cart;
    }

    /** {@inheritdoc} */
    public function addPosition(AbstractCartPosition $position)
    {
        $this->positions[$position->getId()] = $position;
        $this->ensureConsistency();
    }

    /** {@inheritdoc} */
    public function run()
    {
        $this->resetPurchaseResults();
        $this->createPurchaseObjects();
        if (empty($this->purchases)) {
            return;
        }

        $samplePurchase = reset($this->purchases);
        $operation = $samplePurchase::operation();

        try {
            $response = $samplePurchase::perform($operation, $this->collectData(), ['batch' => true]);
            $this->analyzeResponse($response);
        } catch (ResponseErrorException $e) {
            $this->extractResultsFromException($e);
        }
    }

    private function createPurchaseObjects()
    {
        foreach ($this->positions as $id => $position) {
            $this->purchases[$id] = $position->getPurchaseModel();
        }
    }

    private function collectData()
    {
        $result = [];
        foreach ($this->purchases as $id => $purchase) {
            if (!$purchase->validate()) {
                Yii::error("Failed to validate purchase: " . reset($purchase->getFirstErrors()), __METHOD__);
                $this->error[] = new ErrorPurchaseException("Failed to validate purchase. Contact support.", $purchase);
                continue;
            }

            $result[$id] = $purchase->getAttributes();
        }

        return $result;
    }

    private function extractResultsFromException(ResponseErrorException $e)
    {
        $data = $e->getResponse()->getData();

        if (!is_array($data)) {
            Yii::error('Abnormal response during purchase', __METHOD__);
            throw $e;
        }

        $this->analyzeResponse($data);
    }

    protected function analyzeResponse($response) {
        foreach ($response as $key => $item) {
            $this->analyzeResponseItem($key, $item);
        }
    }

    protected function analyzeResponseItem($key, $data)
    {
        if (!isset($this->purchases[$key])) {
            return;
        }

        $purchase = $this->purchases[$key];
        if ($error = $this->getPurchaseErrorFromResponse($purchase, $data)) {
            $this->error[] = new ErrorPurchaseException($error, $purchase);
        } elseif ($pendingMessage = $this->getPurchasePendingFromResponse($purchase, $data)) {
            $this->pending[] = new PendingPurchaseException($pendingMessage, $purchase);
        } else {
            $this->success[] = $purchase;
        }
    }

    /**
     * @param AbstractPurchase $purchase
     * @param array $data
     * @return null|string Error message or `null` when no errors found
     */
    protected function getPurchaseErrorFromResponse(AbstractPurchase $purchase, $data)
    {
        if (is_array($data) && array_key_exists('_error', $data)) {
            return $data['_error'];
        }

        return null;
    }

    /**
     * Override this method to detect pending purchase result.
     *
     * @param AbstractPurchase $purchase
     * @param array $data
     * @return null|string Pending reason or `null` when no errors found
     */
    protected function getPurchasePendingFromResponse(AbstractPurchase $purchase, $data)
    {
        return null;
    }

    protected function ensureConsistency()
    {
        $class = null;
        foreach ($this->positions as $id => $position) {
            if ($class === null) {
                $class = get_class($position);
            }

            if (!$position instanceof $class) {
                throw new InvalidParamException('Position "' . $id . '" is violates position class consistency policy');
            }
        }
    }
}
