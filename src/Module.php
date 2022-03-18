<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance;

use Yii;
use yii\mail\MailerInterface;

/**
 * Class Module.
 */
class Module extends \hipanel\base\Module
{
    public ?string $billServiceEmail = null;

    /**
     * Returns Cart component from Cart module.
     * @return \hiqdev\yii2\cart\ShoppingCart
     */
    public function getCart()
    {
        /** @var \hiqdev\yii2\cart\Module $module */
        $module = Yii::$app->getModule('cart');

        return $module->getCart();
    }

    /**
     * Returns Merchant module.
     * @return \hiqdev\yii2\merchant\Module
     */
    public function getMerchant()
    {
        return Yii::$app->getModule('merchant');
    }

    public function sendBillServiceEmail(string $source, string $scenario, string $subject, string $body): void
    {
        if ($this->billServiceEmail !== null) {
            $mailer = Yii::$container->get(MailerInterface::class);
            $mailer->compose()
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($this->billServiceEmail)
                ->setSubject("$source $scenario: $subject")
                ->setTextBody($body)
                ->send();
        }
    }
}
