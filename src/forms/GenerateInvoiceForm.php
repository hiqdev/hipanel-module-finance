<?php
declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use Yii;
use yii\base\Model;

final class GenerateInvoiceForm extends Model
{
//    public ?string $isBills;
//    public ?string $vatRate;
//    public ?string $date;
//    public ?string $terms;
//    public ?string $documentNumber;
    public ?int $requisite_id = null;
    public ?int $purse_id = null;

    public function attributeLabels(): array
    {
        return [
            'requisite_id' => Yii::t('hipanel:finance', 'Requisite'),
            'purse_id' => Yii::t('hipanel:finance', 'Purse'),
        ];
    }

    public function rules(): array
    {
        return [
            [['requisite_id'], 'required'],
            [['requisite_id', 'purse_id'], 'integer'],
        ];
    }
}
