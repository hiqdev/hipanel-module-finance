<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\forms;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\behaviors\BillQuantity;
use hipanel\modules\finance\logic\bill\QuantityTrait;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\validation\BillChargesSumValidator;
use Yii;
use yii\base\Model;

class BillForm extends Model
{
    use QuantityTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_COPY   = 'copy';

    const EVENT_SHOW_FORM = 'showForm';

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $client_id;

    /**
     * @var integer
     */
    public $requisite_id;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var float
     */
    public $sum;

    /**
     * @var string
     */
    public $time;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $gtype;

    /**
     * @var float
     */
    public $quantity;

    /**
     * @var float
     */
    public $unit;

    /**
     * @var float
     */
    public $userQuantity;

    /**
     * @var string
     */
    public $label;

    /**
     * @var integer
     */
    public $object_id;

    /**
     * @var string
     */
    public $object;

    /**
     * @var string
     */
    public $class;

    /**
     * @var Charge[]
     */
    public $charges = [];

    public function extraFields()
    {
        return ['charges'];
    }

    public function behaviors()
    {
        return [
            [
                'class' => BillQuantity::class,
            ],
        ];
    }

    /**
     * Creates [[BillForm]] from [[Bill]].
     *
     * @param Bill $bill
     * @param string $scenario
     * @return BillForm
     */
    public static function createFromBill($bill, $scenario)
    {
        $attributes = $bill->getAttributes([
            'id', 'object_id', 'client_id', 'currency', 'type',
            'gtype', 'sum', 'time', 'quantity', 'unit', 'label', 'object', 'class', 'requisite_id'
        ]);

        $form = new self(['scenario' => $scenario]);
        $form->setAttributes($attributes, false);
        $form->type = $form->gtype && strpos($form->type, ',') === false ? implode(',', [$form->gtype, $form->type]) : $form->type;

        $form->charges = array_map(function ($model) use ($scenario) {
            $model->scenario = $scenario;

            return $model;
        }, $bill->charges);

        return $form;
    }

    /**
     * @param Bill[] $bills
     * @param string $scenario
     * @return BillForm[]
     */
    public static function createMultipleFromBills($bills, $scenario)
    {
        $result = [];
        foreach ($bills as $bill) {
            $result[] = self::createFromBill($bill, $scenario);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function load($data, $formName = null)
    {
        $this->setAttributes($data);
        $this->loadCharges($data['charges']);

        return true;
    }

    /**
     * @return Charge[]
     */
    public function getCharges()
    {
        if (!empty($this->charges)) {
            return $this->charges;
        }

        return [$this->newCharge()];
    }

    /**
     * Creates new charge.
     *
     * @return Charge
     */
    public function newCharge()
    {
        return new Charge(['scenario' => Charge::SCENARIO_CREATE]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'object_id'], 'integer', 'on' => [self::SCENARIO_UPDATE]],
            [['sum', 'quantity'], 'number', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],
            [['time'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['label', 'currency', 'unit', 'type', 'object', 'class'], 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],
            [['sum'], BillChargesSumValidator::class],
            [['unit'], 'default', 'value' => 'items', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]], // TODO: should be probably replaced with input on client side
            [['object_id', 'requisite_id'], 'integer', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],
            [['currency'], 'filter', 'filter' => 'mb_strtolower'],
            [['currency'], 'currencyValidate', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],
            [['id'], 'required', 'on' => [self::SCENARIO_UPDATE]],
            [
                ['client_id', 'sum', 'quantity', 'unit', 'time', 'currency', 'type'],
                'required',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY],
            ],
            [['!charges'], 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_COPY]],

            [['time', 'object_id', 'type'], function ($attribute) {
                try {
                    Bill::perform('check-unique', $this->attributes);
                } catch (\Exception $e) {
                    $this->addError($attribute, Yii::t('hipanel:finance', 'The bill is not unique'));
                }
            }, 'on' => self::SCENARIO_COPY],
        ];
    }

    public function currencyValidate($attribute, $params, $validator): void
    {
        if (empty($this->client_id)) {
            return;
        }
        $clientCurrencies = Yii::$app->cache->getOrSet('clientCurrencies' . $this->client_id, function (): array {
            $purses = Purse::find()
                        ->where(['client_id' => $this->client_id])
                        ->all();
            return ArrayHelper::getColumn($purses, 'currency');
        }, 3600);
        if (!in_array($this->currency, $clientCurrencies, true)) {
            $this->addError($attribute, Yii::t('hipanel:finance', 'Client hasn\'t purse with this currency'));
        }
    }

    public function attributeLabels()
    {
        return [
            'client_id' => Yii::t('hipanel', 'Client'),
            'time' => Yii::t('hipanel', 'Time'),
            'currency' => Yii::t('hipanel', 'Currency'),
            'sum' => Yii::t('hipanel:finance', 'Sum'),
            'label' => Yii::t('hipanel', 'Description'),
            'type' => Yii::t('hipanel', 'Type'),
            'quantity' => Yii::t('hipanel', 'Quantity'),
            'object_id' => Yii::t('hipanel', 'Object'),
            'requisite' => Yii::t('hipanel:finance', 'Requisite'),
            'requisite_id' => Yii::t('hipanel:finance', 'Requisite'),
        ];
    }

    public function getIsNewRecord()
    {
        return $this->id === null;
    }

    public function forceNewRecord(): void
    {
        $this->id = null;
        $this->time = new \DateTime();
    }

    private function getChargesAsArray()
    {
        return array_filter(array_map(function ($model) {
            /** @var Charge $model */
            if ($model->validate()) {
                return $model->toArray();
            }

            return null;
        }, $this->charges));
    }

    public function fields()
    {
        return [
            'id',
            'client_id',
            'object_id',
            'requisite_id',
            'currency',
            'sum',
            'time',
            'type',
            'quantity',
            'label',
            'object',
            'class',
            'charges' => function () {
                return $this->getChargesAsArray();
            },
        ];
    }

    public function loadCharges($data)
    {
        $charges = [];

        foreach ((array) $data as $datum) {
            $charge = $this->newCharge();
            if ($charge->load($datum, '')) {
                $charge->markAsNotNew();
                $charges[] = $charge;
            }
        }

        $this->charges = $charges;

        return true;
    }

    public function getPrimaryKey()
    {
        return $this->id;
    }

    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * For compatibility with [[hiqdev\hiart\Collection]].
     *
     * @param $defaultScenario
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public function batchQuery($defaultScenario, $data = [], array $options = [])
    {
        $map = [
            'create' => 'create',
            'update' => 'update',
        ];
        $scenario = isset($map[$defaultScenario]) ? $map[$defaultScenario] : $defaultScenario;

        return (new Bill())->batchQuery($scenario, $data, $options);
    }

    public function getOldAttribute($attribute)
    {
        return $this->$attribute;
    }

    public function setOldAttribute($attribute, $value)
    {
        return true;
    }

    public function setOldAttributes($values)
    {
        return true;
    }

    public function afterSave()
    {
        return true;
    }
}
