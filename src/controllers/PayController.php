<?php

namespace hipanel\modules\finance\controllers;

use Yii;
use hipanel\modules\finance\models\Merchant;

class PayController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function actionConfirm()
    {
        Yii::$app->get('hiresource')->setAuth([]);
        $res = Merchant::perform('Pay', $_REQUEST);
        Yii::$app->getResponse()->headers->set('Content-Tupe', 'text/plain');
        if (!$res) $res = 'OK';

        return $res;
    }
}
