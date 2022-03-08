<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\merchant;

use hipanel\modules\finance\models\Merchant;
use hiqdev\hiart\ResponseErrorException;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\yii2\merchant\Collection;
use hiqdev\yii2\merchant\models\DepositRequest;
use hiqdev\yii2\merchant\models\PurchaseRequest;
use Yii;

class PurchaseRequestCollection extends Collection
{
    protected const DEFAULT_DEPOSIT_REQUEST_AMOUNT = 10.0;

    public array $supportedSystems = [
        'webmoney' => 0,
        'paypal' => 1,
        'yandex' => 1,
        'yandexmoney' => 1,
        'interkassa' => 1,
        'paxum' => 1,
        'ecoin' => 1,
        'okpay' => 1,
        'robokassa' => 1,
        'freekassa' => 1,
        'bitpay' => 1,
        'epayservice' => 1,
        'twoco' => 1,
        'epayments' => 1,
        'ikajo' => 1,
        'coingate' => 1,
        'yandexkassa' => 1,
        'cashew' => 1,
    ];

    public function init()
    {
        parent::init();

        if ($this->depositRequest === null) {
            $this->depositRequest = $this->createDefaultDepositRequest();
        }

        if ($this->module->cashewOnly) {
            $this->supportedSystems['cashew'] = 1;
        }

        $this->loadMerchants($this->depositRequest);
    }

    public function loadMerchants($depositRequest)
    {
        $this->addItems($this->fetchMerchants($depositRequest));
    }

    public function fetchMerchants(DepositRequest $depositRequest)
    {
        $params = [
            'transactionId' => $depositRequest->id,
            'sum' => (float)$depositRequest->amount,
            'currency' => $depositRequest->currency,
            'site' => Yii::$app->request->getHostInfo(),
        ];

        if (Yii::$app->user->getIsGuest()) {
            $params['seller'] = Yii::$app->params['user.seller'];
        } elseif ($depositRequest->username !== null) {
            $params['username'] = $depositRequest->username;
        }

        if ($depositRequest->merchant !== null) {
            // When the Request contains concrete merchant name,
            // parameters `finishUrl`, `cancelUrl`, `notifyUrl` contain
            // correct URLs, adjusted by [[hiqdev\yii2-merchant\Module::prepareRequestData()]]
            // and they must be used as success, failure and confirm URLs
            $params['success_url'] = $depositRequest->returnUrl;
            $params['failure_url'] = $depositRequest->cancelUrl;
            $params['confirm_url'] = $depositRequest->notifyUrl;
        }

        try {
            $merchants = $this->requestMerchants($params);
        } catch (ResponseErrorException $e) {
            if ($e->getResponse()->getData() === null) {
                Yii::info('No available payment methods found', 'hipanel:finance');
                $merchants = [];
            } else {
                throw $e;
            }
        }

        $result = [];
        foreach ($merchants as $name => $merchant) {
            if (!empty($this->supportedSystems[$merchant['system']])) {
                $result[$name] = $this->convertMerchant($merchant);
            }
        }

        return $result;
    }

    public function requestMerchants($params)
    {
        $merchantPrepareInfo = fn() => Merchant::perform('prepare-info', $params, ['batch' => true]);

        // Is an empty deposit request, used to get available merchants
        if (empty($params['transactionId']) && empty($params['currency'])
            && $params['sum'] === self::DEFAULT_DEPOSIT_REQUEST_AMOUNT
        ) {
            $userId = Yii::$app->user->getIsGuest() ? null : Yii::$app->user->identity->getId();
            return Yii::$app->getCache()->getOrSet([__METHOD__, $params, $userId], $merchantPrepareInfo, 3600);
        }

        return $merchantPrepareInfo();
    }

    public function convertMerchant($data)
    {
        $request = new PurchaseRequest();

        $request->merchant_name = $data['name'];
        $request->system = $data['system'];
        $request->currency = strtoupper($data['currency']);
        $request->label = $data['label'];
        $request->fee = $data['fee'];
        $request->commission_fee = $data['commission_fee'];
        $request->id = $data['invoice_id'];
        $request->amount = $data['sum'];
        $request->form = (new RedirectPurchaseResponse($data['action'], $data['inputs']))->setMethod($data['method']);
        $request->disableReason = $data['disable_reason'] ?? null;
        $request->vat_rate = $data['vat_rate'] ?? null;
        $request->vat_sum = $data['vat_sum'] ?? null;
        $request->paymentMethod = $data['payment_method'] ?? null;

        return $request;
    }

    protected function createDefaultDepositRequest()
    {
        $request = new DepositRequest();
        $request->amount = self::DEFAULT_DEPOSIT_REQUEST_AMOUNT;

        return $request;
    }
}
