<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\models\query\ChargeQuery;
use Yii;

/**
 * Class Charge.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @property int $id
 * @property string $unit
 * @property string $quantity
 * @property int $parent_id
 * @property string $type
 * @property string $name
 * @property string $ftype
 * @property string $currency
 * @property float $sum
 * @property float $is_payed
 * @property int $object_id
 * @property float $positive
 * @property float $negative
 * @property float $eur_amount
 * @property int $client_id
 * @property string $client
 * @property string $client_type
 * @property array $client_tags
 * @property float $fraction_of_month
 * @property TargetObject $commonObject
 * @property TargetObject $latestCommonObject
 * @property Bill $bill
 */
class Charge extends Resource implements HasSumAndCurrencyAttributesInterface, BillableTimeInterface, FractionAwareInterface
{
    use \hipanel\base\ModelTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    private $isNewRecord;

    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'resource';
    }

    public function rules()
    {
        return [
            [['id', 'type_id', 'object_id', 'parent_id', 'client_id', 'tariff_id', 'seller_id', 'order_id'], 'integer'],
            [['bill_id', 'id'], 'trim'],
            [['class', 'name', 'unit', 'tariff', 'order_name', 'client', 'seller', 'client_type', 'root_ftype'], 'string'],
            [['type', 'label', 'ftype', 'time', 'type_label', 'currency', 'exchange_date', 'client_tags'], 'safe'],
            [['is_payed'], 'boolean'],
            [['fraction_of_month'], 'number'],
            [['sum', 'quantity', 'bill_quantity', 'positive', 'negative', 'discount_sum', 'net_amount', 'rate', 'eur_amount'], 'number'],
            [['unit'], 'default', 'value' => 'items', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['object_id', 'sum', 'type_id', 'quantity', 'unit'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['id'], 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sum' => Yii::t('hipanel', 'Sum'),
            'type' => Yii::t('hipanel', 'Type'),
            'quantity' => Yii::t('hipanel:finance', 'Qty.'),
            'label' => Yii::t('hipanel', 'Description'),
            'time' => Yii::t('hipanel', 'Time'),
            'object_id' => Yii::t('hipanel', 'Object Id'),
            'order_id' => Yii::t('hipanel', 'Order'),
            'is_payed' => Yii::t('hipanel:finance', 'Is paid?'),
            'type_id' => Yii::t('hipanel:finance', 'Type'),
        ]);
    }

    public function markAsNotNew()
    {
        $this->isNewRecord = false;
    }

    public function getCommonObject()
    {
        return $this->hasOne(TargetObject::class, ['id' => 'id']);
    }

    public function getLatestCommonObject()
    {
        return $this->hasOne(TargetObject::class, ['id' => 'id']);
    }

    public function getIsNewRecord()
    {
        return $this->isNewRecord !== false && parent::getIsNewRecord();
    }

    public function getBill()
    {
        return $this->hasOne(Bill::class, ['id' => 'id'])->inverseOf('charges');
    }

    public function getCustomer(): Client
    {
        return new Client([
            'id' => $this->client_id,
            'login' => $this->client,
            'type' => $this->client_type,
            'tags' => $this->client_tags,
        ]);
    }

    public function isMonthly(): bool
    {
        return str_starts_with($this->ftype, 'monthly,');
    }

    /**
     * {@inheritdoc}
     * @return ChargeQuery
     */
    public static function find($options = [])
    {
        return new ChargeQuery(get_called_class(), [
            'options' => $options,
        ]);
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function getBillQuantity()
    {
        return $this->bill_quantity;
    }

    public function getFractionOfMonth(): float
    {
        return $this->fraction_of_month == 0 ? 1.0 : (float)$this->fraction_of_month;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
}
