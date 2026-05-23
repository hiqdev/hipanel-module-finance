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
 * Class InstallmentPlan
 *
 * @property int         $id
 * @property int         $state_id
 * @property string      $state
 * @property string      $state_name
 * @property int         $seller_id
 * @property string      $seller
 * @property int         $client_id
 * @property string      $client
 * @property int         $part_id
 * @property string      $serialno
 * @property int         $model_id
 * @property string      $model
 * @property string      $partno
 * @property int         $brand_id
 * @property string      $brand
 * @property int         $part_type_id
 * @property string      $part_type
 * @property int         $device_id
 * @property string      $device
 * @property string      $since
 * @property string      $till
 * @property int         $quantity
 * @property string      $reason
 * @property string      $expected_monthly_sum
 * @property string      $expected_sum
 * @property string      $charged_sum
 * @property string      $left_sum
 * @property int         $currency_id
 * @property string      $currency
 * @property int|null    $parent_id
 * @property int|null    $child_id
 * @property string|null $note
 * @property array       $items
 * @property InstallmentPlanItem[] $itemsModels
 */
class InstallmentPlan extends \hipanel\base\Model
{
    const string STATE_ONGOING = 'ongoing';
    const string STATE_FINISHED = 'finished';
    const string STATE_BUYOUT = 'buyout';
    const string STATE_INTERRUPTED = 'interrupted';
    const string STATE_AMBIGUOUS = 'ambiguous';
    const string STATE_ADJOURNED = 'adjourned';
    const string STATE_DELETED = 'deleted';

    use \hipanel\base\ModelTrait;

    public static function tableName()
    {
        return 'installmentplan';
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'state_id', 'seller_id', 'client_id', 'part_id', 'model_id', 'brand_id', 'part_type_id', 'device_id', 'currency_id'], 'integer'],
            [['state', 'state_name', 'seller', 'client', 'serialno', 'model', 'partno', 'brand', 'part_type', 'device', 'currency', 'reason', 'order_name', 'company', 'tariff', 'note'], 'string'],
            [['since', 'till', 'warranty_till'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['quantity', 'order_id', 'company_id', 'tariff_id', 'parent_id', 'child_id'], 'integer'],
            [['expected_monthly_sum', 'expected_sum', 'charged_sum', 'left_sum'], 'number'],
            [['items'], 'safe'],
            [['id'], 'required', 'on' => ['delete', 'restore', 'update']],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'seller'                => Yii::t('hipanel:finance:sale', 'Seller'),
            'seller_id'             => Yii::t('hipanel:finance:sale', 'Seller'),
            'client'                => Yii::t('hipanel:finance:sale', 'Buyer'),
            'client_id'             => Yii::t('hipanel:finance:sale', 'Buyer'),
            'part_id'               => Yii::t('hipanel:finance', 'Part No.'),
            'serialno'              => Yii::t('hipanel:finance', 'Serial'),
            'model_id'              => Yii::t('hipanel:finance', 'Model'),
            'model'                 => Yii::t('hipanel:finance', 'Model'),
            'partno'                => Yii::t('hipanel:finance', 'Part No.'),
            'brand_id'              => Yii::t('hipanel:finance', 'Manufacturer'),
            'brand'                 => Yii::t('hipanel:finance', 'Manufacturer'),
            'part_type_id'          => Yii::t('hipanel', 'Type'),
            'part_type'             => Yii::t('hipanel', 'Type'),
            'device_id'             => Yii::t('hipanel:finance:sale', 'Device'),
            'device'                => Yii::t('hipanel:finance:sale', 'Device'),
            'state'                 => Yii::t('hipanel', 'State'),
            'state_id'              => Yii::t('hipanel', 'State'),
            'since'                 => Yii::t('hipanel:finance', 'Installment start'),
            'till'                  => Yii::t('hipanel:finance', 'Installment end'),
            'quantity'              => Yii::t('hipanel:finance', 'Periods'),
            'reason'                => Yii::t('hipanel', 'Reason'),
            'expected_monthly_sum'  => Yii::t('hipanel:finance', 'Monthly sum'),
            'expected_sum'          => Yii::t('hipanel:finance', 'Total sum'),
            'charged_sum'           => Yii::t('hipanel:finance', 'Charged sum'),
            'left_sum'              => Yii::t('hipanel:finance', 'Left sum'),
            'currency'              => Yii::t('hipanel:finance', 'Currency'),
            'currency_id'           => Yii::t('hipanel:finance', 'Currency'),
            'company_id'            => Yii::t('hipanel:finance', 'Company'),
            'order_id'              => Yii::t('hipanel:finance', 'Order'),
            'warranty_till'         => Yii::t('hipanel:finance', 'Warranty till'),
            'parent_id'             => Yii::t('hipanel:finance', 'Parent plan'),
            'child_id'              => Yii::t('hipanel:finance', 'Child plan'),
            'note'                  => Yii::t('hipanel', 'Note'),
        ]);
    }

    public function isDeleted(): bool
    {
        return $this->state === self::STATE_DELETED;
    }

    /**
     * Returns embedded items as InstallmentPlanItem objects.
     *
     * @return InstallmentPlanItem[]
     */
    public function getItems(): array
    {
        $models = [];
        foreach ((array) $this->getAttribute('items') as $item) {
            if ($item instanceof InstallmentPlanItem) {
                $models[] = $item;
                continue;
            }

            $itemData = is_object($item) ? get_object_vars($item) : $item;
            if (!is_array($itemData)) {
                continue;
            }

            if (!isset($itemData['installment_plan_id']) && $this->id !== null) {
                $itemData['installment_plan_id'] = $this->id;
            }

            $models[] = Yii::createObject(['class' => InstallmentPlanItem::class] + $itemData);
        }

        usort($models, static function (InstallmentPlanItem $a, InstallmentPlanItem $b): int {
            return ($a->no ?? PHP_INT_MAX) <=> ($b->no ?? PHP_INT_MAX);
        });

        return $models;
    }

    public function getTariff()
    {
        return $this->hasOne(Plan::class, ['id' => 'tariff_id']);
    }
}
