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

use hipanel\modules\finance\behaviors\BillNegation;
use hipanel\modules\client\models\Client;
use Yii;
use yii\helpers\StringHelper;

/**
 * Class Bill.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @property string $type
 * @property string $unit
 * @property string $quantity
 * @property Charge[] charges
 */
class Bill extends \hipanel\base\Model implements HasSumAndCurrencyAttributesInterface
{
    use \hipanel\base\ModelTrait;

    public $time_from;
    public $time_till;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_TRANSFER = 'create-transfer';

    public static $i18nDictionary = 'hipanel:finance';

    public function behaviors()
    {
        return [
            [
                'class' => BillNegation::class,
                'negativeTypes' => static::negativeTypes(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'seller_id', 'id', 'requisite_id'], 'integer'],
            [['object_id', 'tariff_id'], 'integer'],
            [['client', 'seller', 'bill', 'unit', 'requisite'], 'safe'],
            [['domain', 'server'], 'safe'],
            [['sum', 'balance', 'quantity', 'positive', 'negative', 'opening_balance', 'closing_balance'], 'number'],
            [['currency', 'label', 'descr'], 'safe'],
            [['object', 'domains', 'tariff', 'tariff_type'], 'safe'],
            [['type', 'gtype', 'class', 'ftype'], 'safe'],
            [['class_label'], 'safe'],
            [['is_payed'], 'boolean'],
            [['type_label', 'gtype_label'], 'safe'],
            [['time'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['txn'], 'string'],

            [['id'], 'required', 'on' => [self::SCENARIO_UPDATE, self::SCENARIO_DELETE]],
            [['client_id'], 'integer', 'on' => [self::SCENARIO_CREATE]],
            [['currency', 'sum', 'type', 'label'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            ['sum', function ($attribute, $params, $validator) {
                if ($this->{$attribute} < 0 && in_array($this->type, static::negativeTypes(), true)) {
                    $this->addError($attribute, Yii::t('hipanel:finance', 'The entered value for the selected payment type can not be negative.'));
                }
            }, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['client_id', 'sum', 'time'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['client_id', 'receiver_id', 'currency_id'], 'integer', 'on' => [self::SCENARIO_TRANSFER]],
            [['client_id', 'receiver_id', 'sum', 'currency', 'time'], 'required', 'on' => [self::SCENARIO_TRANSFER]],
            [['client'], 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_TRANSFER]],
            [['receiver'], 'safe', 'on' => [self::SCENARIO_TRANSFER]],
            [['no'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'object' => Yii::t('hipanel', 'Object'),
            'client' => Yii::t('hipanel', 'Client'),
            'time' => Yii::t('hipanel', 'Time'),
            'currency' => Yii::t('hipanel', 'Currency'),
            'quantity' => Yii::t('hipanel', 'Quantity'),
            'balance' => Yii::t('hipanel', 'Balance'),
            'gtype' => Yii::t('hipanel', 'Type'),
            'gtype_label' => Yii::t('hipanel', 'Type'),
            'sum' => Yii::t('hipanel:finance', 'Sum'),
            'tariff' => Yii::t('hipanel:finance', 'Tariff'),
            'tariff_id' => Yii::t('hipanel:finance', 'Tariff'),
            'tariff_type' => Yii::t('hipanel:finance', 'Tariff type'),
            'requisite' => Yii::t('hipanel:finance', 'Requisite'),
            'requisite_id' => Yii::t('hipanel:finance', 'Requisite'),
            'is_payed' => Yii::t('hipanel:finance', 'Is paid?'),
            'txn' => Yii::t('hipanel:finance', 'TXN'),
        ]);
    }

    public function getCharges()
    {
        return $this->hasMany(Charge::class, ['bill_id' => 'id']);
    }

    public function canDelete(): bool
    {
        return !$this->checkClientIsOwner() && $this->canUser('bill.delete');
    }


    public function canEdit(): bool
    {
        return !$this->checkClientIsOwner() && $this->canUser('bill.update');
    }

    public function canCopy(): bool
    {
        return !$this->checkClientIsOwner() && $this->canUser('bill.create');
    }

    public static function negativeTypes()
    {
        return [
            'transfer,minus',
            'correction,negative',
            'overuse,backup_du',
            'overuse,backup_traf',
            'overuse,domain_num',
            'overuse,ip_num',
            'overuse,isp5',
            'overuse,server_traf',
            'overuse,server_traf95',
            'overuse,server_traf95_in',
            'overuse,server_traf95_max',
            'overuse,server_du',
            'overuse,server_traf_in',
            'overuse,server_traf_max',
            'overuse,support_time',
        ];
    }

    protected function checkClientIsOwner(): bool
    {
        $user = Yii::$app->user->identity;
        return in_array($this->client, [$user->username, $user->seller], true);
    }

    protected function canUser(string $role): bool
    {
        return Yii::$app->user->can($role);
    }

    public function getPageTitle(): string
    {
        $title = StringHelper::truncateWords(sprintf('%s: %s %s %s',
            $this->client,
            $this->sum,
            $this->currency,
            $this->label),
            7);
        if (empty($title)) {
            return '&nbsp;';
        }

        return $title;
    }
}
