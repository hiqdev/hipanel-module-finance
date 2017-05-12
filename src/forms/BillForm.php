<?php

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\validation\BillChargesSumValidator;
use Yii;
use yii\base\Model;

class BillForm extends Model
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $client_id;

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
     * @var string
     */
    public $label;

    /**
     * @var Charge[]
     */
    public $charges = [];

    /**
     * Creates [[BillForm]] from [[Bill]]
     *
     * @param Bill $bill
     * @param string $scenario
     * @return BillForm
     */
    public static function createFromBill($bill, $scenario)
    {
        $attributes = $bill->getAttributes(['id', 'client_id', 'currency', 'type', 'gtype', 'sum', 'time', 'quantity', 'label']);

        $form = new self(['scenario' => $scenario]);
        $form->setAttributes($attributes, false);

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
            $result[$bill->id] = self::createFromBill($bill, $scenario);
        }

        return $result;
    }

    /**
     * @inheritdoc
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
     * Creates new charge
     *
     * @return Charge
     */
    public function newCharge()
    {
        return (new Charge(['scenario' => Charge::SCENARIO_CREATE]));
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id'], 'integer', 'on' => [self::SCENARIO_UPDATE]],
            [['sum', 'quantity'], 'number', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['time'], 'date', 'format' => 'php:d.m.Y H:i:s'],
            [['label', 'currency', 'type'], 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['sum'], BillChargesSumValidator::class],

            [['id'], 'required', 'on' => [self::SCENARIO_UPDATE]],
            [
                ['client_id', 'sum', 'quantity', 'time', 'currency', 'type'],
                'required',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE],
            ],
            [['!charges'], 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
        ];
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
        ];
    }

    public function getIsNewRecord()
    {
        return $this->id === null;
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
            'currency',
            'sum',
            'time',
            'type',
            'quantity',
            'label',
            'charges' => function () {
                return $this->getChargesAsArray();
            },
        ];
    }

    public function loadCharges($data)
    {
        $charges = [];

        foreach ((array)$data as $datum) {
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
     * For compatibility with [[hiqdev\hiart\Collection]]
     *
     * @param $defaultScenario
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public function batchQuery($defaultScenario, $data = [], array $options = [])
    {
        $map = [
            'create' => 'create-with-charges',
            'update' => 'update-with-charges',
        ];
        $scenario = isset($map[$defaultScenario]) ? $map[$defaultScenario] : $defaultScenario;

        return (new Bill)->batchQuery($scenario, $data, $options);
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
