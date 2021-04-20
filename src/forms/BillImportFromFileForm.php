<?php

declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use Yii;
use yii\base\Model;

class BillImportFromFileForm extends Model
{
    public $file;

    public $type;

    public $requisite_id;

    public function rules()
    {
        return [
            [['file', 'requisite_id', 'type'], 'required'],
            ['requisite_id', 'integer'],
            ['file', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => ['csv'], 'maxSize' => 1 * 1024 * 1024],
            ['type', 'in', 'range' => array_keys($this->getLinkedTypesAndRequisites())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('hipanel:finance', 'File from the payment system'),
            'type' => Yii::t('hipanel:finance', 'Payment system'),
            'requisite_id' => Yii::t('hipanel:finance', 'Requisite'),
        ];
    }

    public function getLinkedTypesAndRequisites(): array
    {
        return [
            'deposit,epayservice' => null,
            'deposit,paxum' => null,
            'deposit,cardpay_dwgg' => 'DataWeb Global Group BV - CardPay',
            'deposit,paypal' => null,
            'deposit,dwgg_transferwise' => 'DataWeb Global Group BV - TransferWise',
        ];
    }

    public function getRequisiteNames(): array
    {
        return array_values(array_filter($this->getLinkedTypesAndRequisites()));
    }

    public function guessTypeByRequisiteName(string $name): void
    {
        $names = array_values($this->getLinkedTypesAndRequisites());
        if (in_array($name, $names, true)) {
            $this->type = array_search($name, $this->getLinkedTypesAndRequisites(), true);
        }
    }
}
