<?php

declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\providers\BillTypesProvider;
use Yii;
use yii\base\Model;

class BillImportFromFileForm extends Model
{
    public $file;

    public $type;

    private BillTypesProvider $billTypesProvider;

    public function init(): void
    {
        parent::init();
        $this->billTypesProvider = Yii::$container->get(BillTypesProvider::class);
    }

    public function rules()
    {
        return [
            [['file', 'type'], 'required'],
            ['file', 'file', 'skipOnEmpty' => false, 'extensions' => ['csv'], 'maxSize' => 1 * 1024 * 1024 * 1024],
            ['type', 'in', 'range' => array_keys($this->getPaymentSystemDropdownList())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('hipanel:finance', 'File from the payment system'),
            'type' => Yii::t('hipanel:finance', 'Payment system'),
        ];
    }

    public function getPaymentSystemDropdownList(): array
    {
        $types = $this->billTypesProvider->getTypesList();

        return array_filter($types, static fn($label, $type): bool => in_array($type, [
            'deposit,epayservice',
            'deposit,paxum'
//            'deposit,dwgg_epayments',
//            'deposit,cardpay_dwgg',
//            'deposit,paypal',
        ], true), ARRAY_FILTER_USE_BOTH);

    }
}
