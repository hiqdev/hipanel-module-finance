<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\models\Tariff;
use Yii;
use yii\web\NotFoundHttpException;

class TariffManagerFactory
{
    /**
     * @param integer $id Tariff ID
     * @return AbstractTariffManager|object
     * @throws NotFoundHttpException
     */
    public static function createById($id)
    {
        $model = Tariff::find()->byId($id)->details()->one();

        if ($model === null) {
            throw new NotFoundHttpException('Tariff was not found');
        }

        return Yii::createObject(static::buildClassName($model->type), [$model]);
    }

    /**
     * @param string $type Tariff type
     * @return AbstractTariffManager|object
     */
    public static function createByType($type)
    {
        return Yii::createObject(static::buildClassName($type));
    }

    /**
     * @param string $type Tariff type
     * @return string
     */
    protected static function buildClassName($type)
    {
        return 'hipanel\modules\finance\logic\\' . ucfirst($type) . 'TariffManager';
    }
}
