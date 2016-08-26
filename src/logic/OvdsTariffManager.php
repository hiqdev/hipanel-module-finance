<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\VdsTariffForm;
use hipanel\modules\finance\models\Tariff;
use hiqdev\hiart\ErrorResponseException;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class OvdsTariffManager extends VdsTariffManager
{
    public $type = 'ovds';


}
