<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Tariff;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class TariffManagerFactory
{
    /**
     * @param integer $id Tariff ID
     * @param array $options that will be passed to the object as configuration
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @return AbstractTariffManager|object
     */
    public static function createById($id, $options = [])
    {
        if (empty($id)) {
            throw new BadRequestHttpException('ID is missing');
        }

        $model = Tariff::find()->byId($id)->details()->one();

        if ($model === null) {
            throw new NotFoundHttpException('Tariff was not found');
        }

        $model->scenario = ArrayHelper::getValue($options, 'scenario', $model::SCENARIO_DEFAULT);

        return Yii::createObject(array_merge([
            'class' => static::buildClassName($model->type),
            'tariff' => $model,
        ], $options));
    }

    /**
     * @param string $type Tariff type
     * @param integer $parent_id the parent tariff id
     * @param array $options that will be passed to the object as configuration
     * @return AbstractTariffManager|object
     */
    public static function createByType($type, $parent_id = null, $options = [])
    {
        $options = array_merge([
            'parent_id' => $parent_id,
            'formOptions' => [
                'scenario' => 'create',
            ],
        ], $options);

        return Yii::createObject(array_merge(['class' => static::buildClassName($type)], $options));
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
