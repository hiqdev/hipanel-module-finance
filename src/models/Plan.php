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

use hipanel\base\ModelTrait;
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
 *
 * @property Sale[] $sales
 * @property Price[]|CertificatePrice[] $prices
 * @property-read string[] typeOptions
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Plan extends \hipanel\base\Model
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
            [['your_tariff'], 'boolean'],
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
        ]);
    }

    public function getPrices()
    {
        if ($this->type === Plan::TYPE_CERTIFICATE) {
            return $this->hasMany(CertificatePrice::class, ['plan_id' => 'id'])->inverseOf('plan');
        }

        return $this->hasMany(Price::class, ['plan_id' => 'id'])->indexBy('id')->inverseOf('plan');
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

    public function supportsSharedPrices(): bool
    {
        return !\in_array($this->type, [Plan::TYPE_TEMPLATE, Plan::TYPE_CERTIFICATE, Plan::TYPE_DOMAIN], true);
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
}
