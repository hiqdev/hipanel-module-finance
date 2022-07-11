<?php
declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use Yii;
use yii\base\Model;

final class PrepareInvoiceForm extends Model
{
    public ?int $requisite_id = null;
    public ?int $purse_id = null;
    public ?string $vat_rate = null;
    public ?string $bill_ids = null;
    public ?string $month = null;
    public bool $takeOutCharges = false;

    public function attributeLabels(): array
    {
        return [
            'requisite_id' => Yii::t('hipanel:finance', 'Requisite'),
            'purse_id' => Yii::t('hipanel:finance', 'Purse'),
            'takeOutCharges' => Yii::t('hipanel:finance', 'Take out charges'),
            'month' => Yii::t('hipanel:finance', 'Month'),
            'vat_rate' => Yii::t('hipanel:finance', 'VAT rate'),
        ];
    }

    public function rules(): array
    {
        return [
            [['requisite_id', 'bill_ids'], 'required'],
            [['requisite_id', 'purse_id'], 'integer'],
            [['takeOutCharges'], 'boolean'],
            [['vat_rate'], 'number'],
            [['month'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }
}
