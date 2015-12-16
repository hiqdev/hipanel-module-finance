<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\base\Err;
use hipanel\modules\finance\models\Merchant;
use hiqdev\hiart\HiArtException;
use Yii;

/**
 * Class PayController
 * @package hipanel\modules\finance\controllers
 */
class PayController extends \yii\web\Controller
{
    /**
     * {@inheritdoc}
     * @var \hipanel\modules\finance\Module
     */
    public $module;

    /**
     * {@inheritdoc}
     */
    public $enableCsrfValidation = false;

    /**
     * Action is designed to get the system notification from payment system,
     * process it and report success or error for the payment system.
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionNotify()
    {
        Yii::info(http_build_query($_REQUEST), 'merchant');
        Yii::$app->get('hiresource')->setAuth([]);
        try {
            $result = Merchant::perform('Pay', $_REQUEST);
        } catch (HiArtException $e) {
            $result = Err::set($_REQUEST, $e->getMessage());
        }

        return $this->module->getMerchant()->renderNotify($result);
    }
}
