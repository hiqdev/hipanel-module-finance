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

use hipanel\base\Model;
use hipanel\base\ModelTrait;
use hipanel\behaviors\CustomAttributes;
use hipanel\models\Ref;
use hipanel\modules\finance\models\query\PlanQuery;
use Yii;

/**
 * Class Plan.
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $currency
 * @property string $state
 * @property int $currency_id
 * @property bool $is_grouping
 * @property bool $your_tariff
 *
 * @property PriceHistory[] $priceHistory
 * @property Sale[] $sales
 * @property Price[]|CertificatePrice[] $prices
 * @property-read string[] typeOptions
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Plan extends Model
{
    public const TYPE_SERVER = 'server';
    public const TYPE_PCDN = 'pcdn';
    public const TYPE_VCDN = 'vcdn';
    public const TYPE_TEMPLATE = 'template';
    public const TYPE_CERTIFICATE = 'certificate';
    public const TYPE_DOMAIN = 'domain';
    public const TYPE_SWITCH = 'switch';
    public const TYPE_AVDS = 'avds';
    public const TYPE_OVDS = 'ovds';
    public const TYPE_SVDS = 'svds';
    public const TYPE_CLIENT = 'client';
    public const TYPE_HARDWARE = 'hardware';
    public const TYPE_ANYCASTCDN = 'anycastcdn';
    public const TYPE_REFERRAL = 'referral';
    public const TYPE_VPS = 'vps';
    public const TYPE_SNAPSHOT = 'snapshot';
    public const TYPE_VOLUME = 'volume';
    public const TYPE_STORAGE = 'storage';
    public const TYPE_PRIVATE_CLOUD_BACKUP = 'private_cloud_backup';
    public const TYPE_PRIVATE_CLOUD = 'private_cloud';
    public const TYPE_CALCULATOR = 'calculator';
    public const TYPE_VIDECDN = 'videocdn';
    public const TYPE_LOAD_BALANCER = 'load_balancer';

    protected $knownTypes = [
        self::TYPE_SERVER               => self::TYPE_SERVER,
        self::TYPE_PCDN                 => self::TYPE_PCDN,
        self::TYPE_VCDN                 => self::TYPE_VCDN,
        self::TYPE_TEMPLATE             => self::TYPE_TEMPLATE,
        self::TYPE_CERTIFICATE          => self::TYPE_CERTIFICATE,
        self::TYPE_DOMAIN               => self::TYPE_DOMAIN,
        self::TYPE_SWITCH               => self::TYPE_SWITCH,
        self::TYPE_AVDS                 => self::TYPE_AVDS,
        self::TYPE_OVDS                 => self::TYPE_OVDS,
        self::TYPE_SVDS                 => self::TYPE_SVDS,
        self::TYPE_CLIENT               => self::TYPE_CLIENT,
        self::TYPE_HARDWARE             => self::TYPE_HARDWARE,
        self::TYPE_ANYCASTCDN           => self::TYPE_ANYCASTCDN,
        self::TYPE_VPS                  => self::TYPE_VPS,
        self::TYPE_SNAPSHOT             => self::TYPE_SNAPSHOT,
        self::TYPE_VOLUME               => self::TYPE_VOLUME,
        self::TYPE_STORAGE              => self::TYPE_STORAGE,
        self::TYPE_PRIVATE_CLOUD_BACKUP => self::TYPE_PRIVATE_CLOUD_BACKUP,
        self::TYPE_PRIVATE_CLOUD        => self::TYPE_PRIVATE_CLOUD,
        self::TYPE_REFERRAL             => self::TYPE_REFERRAL,
        self::TYPE_CALCULATOR           => self::TYPE_CALCULATOR,
        self::TYPE_VIDECDN              => self::TYPE_VIDECDN,
        self::TYPE_LOAD_BALANCER        => self::TYPE_LOAD_BALANCER,
    ];

    use ModelTrait;

    /**
     * @var string
     */
    public $monthly;

    public $servers = [];

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'type_id', 'state_id', 'client_id', 'currency_id'], 'integer'],
            [['type', 'state', 'client', 'name', 'plan', 'note', 'currency', 'is_grouping'], 'string'],

            [['type', 'name', 'currency'], 'required', 'on' => ['create', 'update']],
            [['id'], 'required', 'on' => ['update', 'delete', 'set-note']],
            [['id'], 'required', 'on' => ['delete', 'restore']],
            [['id', 'server_ids'], 'safe', 'on' => ['copy']],
            [['your_tariff', 'is_saled'], 'boolean'],
            [['fee'], 'number'],
            [['custom_attributes', 'data'], 'safe', 'on' => ['create', 'update']],
        ]);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'as customAttributes' => CustomAttributes::class,
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'client' => Yii::t('hipanel', 'Seller'),
            'name' => Yii::t('hipanel:finance', 'Name'),
            'server_ids' => Yii::t('hipanel.finance.plan', 'Servers'),
            'monthly' => Yii::t('hipanel.finance.plan', 'Monthly'),
            'is_grouping' => Yii::t('hipanel.finance.plan', 'Grouping'),
            'currency' => Yii::t('hipanel:finance', 'Currency'),
            'is_saled' => Yii::t('hipanel:finance', 'Is saled?'),
            'fee' => Yii::t('hipanel:finance', 'Subscription fee'),
        ]);
    }

    public function getPrices()
    {
        if ($this->type === Plan::TYPE_CERTIFICATE) {
            return $this->hasMany(CertificatePrice::class, ['plan_id' => 'id'])->inverseOf('plan');
        }

        return $this->hasMany(Price::class, ['plan_id' => 'id'])->indexBy('id')->inverseOf('plan');
    }

    public function getPriceHistory()
    {
        return $this->hasMany(PriceHistory::class, ['tariff_id' => 'id']);
    }

    public function getDesiredPriceClass()
    {
        if ($this->type === Plan::TYPE_CERTIFICATE) {
            return CertificatePrice::class;
        }

        return Price::class;
    }

    public function getSales()
    {
        return $this->hasMany(Sale::class, ['tariff_id' => 'id']);
    }

    public function getTypeOptions()
    {
        return Ref::getList('type,tariff');
    }

    public function getStateOptions()
    {
        return Ref::getList('state,tariff');
    }

    public function isDeleted(): bool
    {
        return $this->state === 'deleted';
    }

    public function supportsIndividualPricesCreation(): bool
    {
        return ! in_array($this->type, [
            // Types listed here does not support individual prices creation


        ], true);
    }

    public function supportsSharedPricesCreation(): bool
    {
        return ! in_array($this->type, [
            self::TYPE_TEMPLATE,
            self::TYPE_CERTIFICATE,
            self::TYPE_DOMAIN,
        ], true);
    }

    /**
     * {@inheritdoc}
     * @return PlanQuery
     */
    public static function find($options = [])
    {
        return new PlanQuery(get_called_class(), [
            'options' => $options,
        ]);
    }

    public function isKnownType(string $type = null): bool
    {
        return isset($this->knownTypes[$type ?: $this->type]);
    }
}
