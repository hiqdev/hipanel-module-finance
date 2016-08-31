<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;
use Yii;

/**
 * Class DomainResource
 * @package hipanel\modules\finance\models
 */
class ServerResource extends Resource
{
    use ModelTrait;

    public static function index()
    {
        return 'resources';
    }

    public static function type()
    {
        return 'resource';
    }

    const MODEL_TYPE_CPU = 'cpu';
    const MODEL_TYPE_RAM = 'ram';
    const MODEL_TYPE_HDD = 'hdd';
    const MODEL_TYPE_CHASSIS = 'chassis';

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['model_type', 'partno'], 'safe'];

        return $rules;
    }

    /**
     * @return array
     */
    public function getModelTypes()
    {
        return [
            static::MODEL_TYPE_CPU => Yii::t('hipanel/finance/tariff', 'CPU'),
            static::MODEL_TYPE_RAM => Yii::t('hipanel/finance/tariff', 'RAM'),
            static::MODEL_TYPE_HDD => Yii::t('hipanel/finance/tariff', 'HDD'),
            static::MODEL_TYPE_CHASSIS => Yii::t('hipanel/finance/tariff', 'Chassis'),
        ];
    }

    public function isModelTypeCorrect()
    {
        return isset($this->getModelTypes()[$this->model_type]);
    }
}
