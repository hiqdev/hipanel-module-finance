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
use Yii;

class PayController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function actionNotify()
    {
        Yii::info(http_build_query($_REQUEST), 'merchant');
        Yii::$app->get('hiresource')->setAuth([]);
        try {
            $res = Merchant::perform('Pay', $_REQUEST);
        } catch (\Exception $e) {
            $res = Err::set($_REQUEST, $e->getMessage());
        }

        return $this->module->getMerchant()->renderNotify($res);
    }
}
