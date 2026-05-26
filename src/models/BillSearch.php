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

    public function rules(): array
    {
        return ArrayHelper::merge($this->defaultRules(), [
            [['time_from', 'time_till'], 'date', 'format' => 'php:Y-m-d'],
            [['servers', 'server_ids', 'object_types'], 'safe'],
            [['ftype'], 'safe'],
        ]);
    }

    public static function tableName(): string
    {
        return Bill::tableName();
    }

    public function searchAttributes(): array
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'time_from',
            'time_till',
            'servers',
            'server_ids',
            'server_ilike',
            'ftype',
            'object_name_ilike',
            'object_types',
            'type_ids',
            'charge_type_ids',
            'client_types',
            'client_tags',
        ]);
    }

    public function attributeLabels(): array
    {
        return $this->mergeAttributeLabels([
            'servers' => Yii::t('hipanel:finance', 'Servers'),
            'object_name_ilike' => Yii::t('hipanel:finance', 'Object name'),
            'object_types' => Yii::t('hipanel:finance', 'Object types'),
            'type_id' => Yii::t('hipanel:finance', 'Type'),
            'client_types' => Yii::t('hipanel:finance', 'Client types'),
            'client_tags' => Yii::t('hipanel:finance', 'Client tags'),
        ]);
    }

    public function getObjectTypes(): array
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
