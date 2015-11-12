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

use hipanel\modules\finance\models\Merchant;
use Yii;

class PayController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function actionConfirm()
    {
        Yii::$app->get('hiresource')->setAuth([]);
        $res = Merchant::perform('Pay', $_REQUEST);
        Yii::$app->getResponse()->headers->set('Content-Tupe', 'text/plain');
        if (!$res) {
            $res = 'OK';
        }

        return $res;
    }
}
