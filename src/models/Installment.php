<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2026, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\modules\finance\models\query\SaleQuery;
use hipanel\modules\server\models\Server;
use hiqdev\hiart\ActiveQuery;
use Yii;

/**
 * Class Installment.
 *
 * @author Yurii Myronchuk <bladeroot@gmail.com>
 */
class Installment extends \hipanel\base\Model
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'client_id', 'seller_id', 'part_id', 'model_id', 'model_type_id', 'model_brand_id', 'device_id'], 'integer'],
            [['client', 'seller', 'serial', 'model', 'model_type', 'model_type_label', 'model_brand', 'model_brand_label', 'device'], 'string'],
            [['start', 'finish'], 'datetime', 'format' => 'php:Y-m-d'],
            [['period'], 'integer'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'client' => Yii::t('hipanel:finance:sale', 'Buyer'),
            'client_id' => Yii::t('hipanel:finance:sale', 'Buyer'),
            'seller' => Yii::t('hipanel:finance:sale', 'Seller'),
            'seller_id' => Yii::t('hipanel:finance:sale', 'Seller'),
            'part_id' => Yii::t('hipanel:stock', 'Part No.'),
            'serial' => Yii::t('hipanel:stock', 'Serial'),
            'model_id' => Yii::t('hipanel:stock', 'Part No.'),
            'model' => Yii::t('hipanel:stock', 'Part No.'),
            'model_type_id' => Yii::t('hipanel', 'Type'),
            'model_type' => Yii::t('hipanel', 'Type'),
            'model_type_label' => Yii::t('hipanel', 'Type'),
            'model_brand_id' => Yii::t('hipanel:stock', 'Manufacturer'),
            'model_brand' => Yii::t('hipanel:stock', 'Manufacturer'),
            'model_brand_label' => Yii::t('hipanel:stock', 'Manufacturer'),
            'device_id' => Yii::t('hipanel:finance:sale', 'Device'),
            'device' => Yii::t('hipanel:finance:sale', 'Device'),
            'start' => Yii::t('hipanel:finance:sale', 'Installment start'),
            'finish' => Yii::t('hipanel:finance:sale', 'Installment end'),
            'period' => Yii::t('hipanel:finance:sale', 'Periods left'),
        ]);
    }
}
