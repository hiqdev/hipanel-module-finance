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

use hipanel\modules\finance\models\query\SaleQuery;
use hipanel\modules\server\models\Server;
use hiqdev\hiart\ActiveQuery;
use Yii;

/**
 * Class Sale.
 *
 * @property string|int $id
 * @property string $object
 * @property string|int $object_id
 * @property string|int $tariff_id
 * @property string $tariff_type
 * @property string|int $buyer_id
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Sale extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    const SALE_TYPE_IP = 'ip';
    const SALE_TYPE_DEVICE = 'device';
    const SALE_TYPE_SERVER = 'server';
    const SALE_TYPE_PCDN = 'pcdn';
    const SALE_TYPE_VCDN = 'vcdn';
    const SALE_TYPE_SWITCH = 'switch';
    const SALE_TYPE_ACCOUNT = 'account';
    const SALE_TYPE_CLIENT = 'client';
    const SALE_TYPE_PART = 'part';
    const SALE_TYPE_HARDWARE = 'model_group';
    const SALE_TYPE_PRIVATE_CLOUD = 'private_cloud';
    const SALE_TYPE_VIDEOCDN = 'videocdn';
    const SALE_TYPE_SNAPSHOT = 'snapshot';
    const SALE_TYPE_ANYCASTCDN = 'anycastcdn';
    const SALE_TYPE_PRIVATE_CLOUD_BACKUP = 'private_cloud_backup';
    const SALE_TYPE_STORAGE = 'storage';
    const SALE_TYPE_VPS = ' vps';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'buyer_id', 'seller_id', 'object_id', 'tariff_id', 'currency_id'], 'integer'],
            [[
                'object',
                'object_like',
                'object_label',
                'seller',
                'login',
                'buyer',
                'tariff',
                'time',
                'unsale_time',
                'expires',
                'renewed_num',
                'sub_factor',
                'tariff_type',
                'is_grouping',
                'from_old',
                'currency',
                'tariff_created_at',
                'tariff_updated_at'
            ], 'string'],
            [['object_type'], 'safe'],
            [['id'], 'required', 'on' => 'delete'],
            [['id', 'tariff_id', 'time'], 'required', 'on' => 'update'],
            [['tariff_id', 'time', 'object_id', 'buyer_id', 'seller_id'], 'required', 'on' => 'create'],
            [['tariff_id', 'time', 'buyer_id'], 'required', 'on' => 'change-buyer'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'time' => Yii::t('hipanel:finance:sale', 'Time'),
            'unsale_time' => Yii::t('hipanel:finance:sale', 'Close time'),
            'object' => Yii::t('hipanel:finance:sale', 'Object'),
            'object_like' => Yii::t('hipanel:finance:sale', 'Object'),
            'object_type' => Yii::t('hipanel:finance:sale', 'Object Types'),
            'object_label' => Yii::t('hipanel:finance:sale', 'Description'),
            'buyer' => Yii::t('hipanel:finance:sale', 'Buyer'),
            'buyer_id' => Yii::t('hipanel:finance:sale', 'Buyer'),
            'seller' => Yii::t('hipanel:finance:sale', 'Seller'),
            'seller_id' => Yii::t('hipanel:finance:sale', 'Seller'),
            'tariff_id' => Yii::t('hipanel:finance', 'Tariff'),
            'tariff_type' => Yii::t('hipanel', 'Type'),
            'currency' => Yii::t('hipanel', 'Currency'),
        ]);
    }

    public function getTypes()
    {
        return array_filter([
            self::SALE_TYPE_ACCOUNT => Yii::t('hipanel', 'Accounts'),
            self::SALE_TYPE_ANYCASTCDN => 'Anycastcdn',
            self::SALE_TYPE_CLIENT => Yii::t('hipanel', 'Clients'),
            self::SALE_TYPE_DEVICE => 'Device',
            self::SALE_TYPE_IP => 'IP',
            self::SALE_TYPE_PART => Yii::getAlias('@part', false) ? Yii::t('hipanel:stock', 'Parts') : null,
            self::SALE_TYPE_PRIVATE_CLOUD => 'Private cloud',
            self::SALE_TYPE_PRIVATE_CLOUD_BACKUP => 'Private cloud backup',
            self::SALE_TYPE_SNAPSHOT => 'Snapshot',
            self::SALE_TYPE_STORAGE => 'Storage',
            self::SALE_TYPE_VPS => ' Vps',
            self::SALE_TYPE_VIDEOCDN => 'Videocdn',
        ]);
    }

    public static function find(array $options = []): SaleQuery
    {
        return new SaleQuery(get_called_class(), [
            'options' => $options,
        ]);
    }

    public function getServer(): ActiveQuery
    {
        return $this->hasOne(Server::class, ['id' => 'object_id'])
            ->withBindings()
            ->withHardwareSettings();
    }
}
