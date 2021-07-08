<?php

declare(strict_types=1);

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\models\Requisite;
use yii\base\Model;
use Yii;

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
            ['type', 'in', 'range' => $this->getDepositTypes()],
            ['fee_type', 'in', 'range' => $this->getFeeTypes()],
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

    public function getDepositTypes(): array
    {
        foreach ($this->getRequisitesTypes() as $type => $data) {
            $deposits[$data['deposit']] = $data['deposit'];
        }

        return $deposits;
    }

    public function getFeeTypes(): array
    {
        foreach ($this->getRequisitesTypes() as $type => $data) {
            $fees[$data['fee']] = $data['fee'];
        }

        return array_filter($fees);
    }

    public function getClientSubstrings(): ?array
    {
        $data = $this->getImportData('client.substrings');

        return empty($data) ? null : $data;
    }

    public function guessRequisiteType(): string
    {
        $requisite = $this->getRequisite();
        $types = $this->getRequisitesTypes();
        foreach ($types as $key => $data) {
            if (preg_match("/{$key}/ui", $requisite->name)) {
                $this->type = $types[$key]['deposit'] ?? null;
                if ($this->type === null) {
                    continue;
                }
                $this->fee_type = $types[$key]['fee'] ?? null;
                return $key;
            }
        }

        throw new RuntimeException(Yii::t('hipanel:finance', 'None of the existing import parsers is associated with the selected requisite. Choose a different requisite.'));
    }

    public function getRequisiteTypes(): ?array
    {
        return array_keys($this->getRequisitesTypes());
    }

    public function getRequisitesTypes(): ?array
    {
        return $this->getImportData("requisite.types") ?? [];
    }

    public function getRequisite(): ?Requisite
    {
        return Yii::$app->cache->getOrSet([__CLASS__, __METHOD__, $this->requisite_id], function() {
            return Requisite::find()->where(['id' => $this->requisite_id])->one();
        }, 3600);
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
