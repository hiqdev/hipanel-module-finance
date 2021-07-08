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

    public $fee_type;

    public function rules()
    {
        return [
            [['file', 'requisite_id', 'type'], 'required'],
            ['requisite_id', 'integer'],
            ['file', 'file', 'skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => ['csv'], 'maxSize' => 1 * 1024 * 1024],
            ['type', 'in', 'range' => array_unique(array_values($this->getLinkedTypesAndRequisites()))],
            ['fee_type', 'in', 'range' => array_unique(array_values($this->getLinkedFeeTypesAndRequisites()))],
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

    /**
     * This method should be return associative array where the keys are bill types and the values are the names of the requisites
     *
     * Example: [
     *  BILL_TYPE => REQUISITE_NAME,
     *  BILL_TYPE => REQUISITE_NAME,
     *  ...
     * ]
     * @return array
     */
    public function getLinkedTypesAndRequisites(): array
    {
        return $this->getLinkedAttributesAndRequisites('names');
    }

    public function getLinkedFeeTypesAndRequisites(): array
    {
        return $this->getLinkedAttributesAndRequisites('fee_names');
    }

    public function getRequisiteNames(): array
    {
        return array_keys($this->getLinkedTypesAndRequisites());
    }

    public function guessTypeByRequisiteName(string $name): void
    {
        $type = $this->guessAttributeByRequisiteName('type', $name);
        if ($type === null) {
            throw new \RuntimeException(Yii::t('hipanel:finance', 'None of the existing import parsers is associated with the selected requisite. Choose a different requisite.'));
        }

        $this->type = $type;
    }

    public function guessFeeTypeByRequisiteName(string $name): void
    {
        $this->fee_type = $this->guessAttributeByRequisiteName('fee_type', $name);
    }

    public function guessAttributeByRequisiteName(string $attribute, string $name): ?string
    {
        $map = $attribute === 'type'
            ? $this->getLinkedTypesAndRequisites()
            : $this->getLinkedFeeTypesAndRequisites();

        return $map[$name] ?? null;
    }

    public function getClientSubstrings(): ?array
    {
        $data = $this->getImportData('client.substrings');

        return empty($data) ? null : $data;
    }

    protected function getLinkedAttributesAndRequisites(string $attribute): array
    {
        return $this->getImportData("requisite.{$attribute}");
    }

    protected function getImportData(string $name): array
    {
        $data = Yii::$app->params['module.finance.bill.import'];
        if (empty($data)) {
            return [];
        }

        return $data[$name] ?? [];
    }
}
