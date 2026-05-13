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

use Yii;

/**
 * Class InstallmentPlanItem represents a single payment period within an InstallmentPlan.
 *
 * @property int         $id
 * @property int         $installment_plan_id
 * @property string      $month
 * @property int         $no
 * @property string      $sum
 * @property string      $currency
 * @property int|null    $charge_id
 * @property string|null $charge_sum
 * @property int|null    $bill_id
 */
class InstallmentPlanItem extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public static function tableName(): string
    {
        return 'installmentplanitem';
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'installment_plan_id', 'charge_id', 'bill_id', 'no', 'tariff_id'], 'integer'],
            [['month', 'currency', 'tariff'], 'string'],
            [['sum', 'charge_sum'], 'number'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'no'                  => Yii::t('hipanel', '#'),
            'month'               => Yii::t('hipanel', 'Month'),
            'sum'                 => Yii::t('hipanel:finance', 'Sum'),
            'currency'            => Yii::t('hipanel:finance', 'Currency'),
            'charge_sum'          => Yii::t('hipanel:finance', 'Charged'),
            'charge_id'           => Yii::t('hipanel:finance', 'Charge'),
            'bill_id'             => Yii::t('hipanel:finance', 'Bill'),
            'installment_plan_id' => Yii::t('hipanel:finance', 'Installment plan'),
        ]);
    }

    public function getInstallmentPlan()
    {
        return $this->hasOne(InstallmentPlan::class, ['id' => 'installment_plan_id']);
    }

    public function getCharge()
    {
        return $this->hasOne(Charge::class, ['id' => 'charge_id']);
    }

    public function getBill()
    {
        return $this->hasOne(Bill::class, ['id' => 'bill_id']);
    }

    public function getTariff()
    {
        return $this->hasOne(Plan::class, ['id' => 'tariff_id']);
    }

    public function isPaid(): bool
    {
        return $this->charge_id !== null;
    }
}
