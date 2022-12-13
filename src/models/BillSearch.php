<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;
use hipanel\helpers\ArrayHelper;
use Yii;

class BillSearch extends Bill
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
        rules as defaultRules;
    }

    public function rules()
    {
        return ArrayHelper::merge($this->defaultRules(), [
            [['time_from', 'time_till'], 'date', 'format' => 'php:Y-m-d'],
            [['servers', 'server_ids', 'object_types'], 'safe'],
            [['ftype'], 'safe'],
        ]);
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'time_from', 'time_till',
            'servers', 'server_ids',
            'ftype', 'object_name_ilike',
            'object_types', 'type_ids',
        ]);
    }

    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'servers' => Yii::t('hipanel:finance', 'Servers'),
            'object_name_ilike' => Yii::t('hipanel:finance', 'Object name'),
            'object_types' => Yii::t('hipanel:finance', 'Object types'),
            'type_id' => Yii::t('hipanel:finance', 'Type'),
        ]);
    }

    public function getObjectTypes()
    {
        return [
            'volume' => Yii::t('hipanel:finance', 'Volumes'),
            'anycastcdn' => Yii::t('hipanel:finance', 'AnycastCDN'),
            'videocdn' => Yii::t('hipanel:finance', 'VideoCDN'),
            'private_cloud' => Yii::t('hipanel:finance', 'Private cloud'),
            'private_cloud_backup' => Yii::t('hipanel:finance', 'Private cloud backup'),
            'snapshot' => Yii::t('hipanel:finance', 'Snapshot'),
            'storage' => Yii::t('hipanel:finance', 'Storage'),
            'backup' => Yii::t('hipanel:finance', 'Backup'),
            'cloudservers' => Yii::t('hipanel:finance', 'Cloud servers'),
        ];
    }
}
