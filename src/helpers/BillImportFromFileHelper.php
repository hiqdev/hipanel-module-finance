<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\Requisite;
use Yii;

class BillImportFromFileHelper
{
    protected int $requisiteID;

    protected Requisite $requisite;

    protected array $requisitesTypes;

    protected string $requisiteType;

    protected string $depositType;

    protected ?array $clientSubstrings;

    protected ?string $feeType;

    public function __construct(?int $id = null)
    {
        $this->requisitesTypes = $this->guessRequisitesTypes();
        $this->clientSubstrings = $this->guessClientSubstrings();

        if ($id !== null) {
            $this->requisiteID = $id;
            $this->requisite = $this->guessRequisite();
            $this->requisiteType = $this->guessRequisiteType();
            $this->depositType = $this->guessDepositType();
            $this->feeType = $this->guessFeeType();
        }
    }

    public function getRequisiteID(): ?int
    {
        return $this->requisiteID;
    }

    public function getRequisite(): Requisite
    {
        return $this->requisite;
    }

    public function getRequisitesTypes(): array
    {
        return $this->requisitesTypes;
    }

    public function getRequisiteType(): string
    {
        return $this->requisiteType;
    }

    public function getDepositType(): string
    {
        return $this->depositType;
    }

    public function getFeeType(): ?string
    {
        return $this->feeType;
    }

    public function getClientSubstrings(): ?array
    {
        return $this->clientSubstrings;
    }

    protected function guessRequisite(): ?Requisite
    {
        return Yii::$app->cache->getOrSet([__CLASS__, __METHOD__, $this->requisiteID], function() {
            return Requisite::find()->where(['id' => $this->requisiteID])->one();
        });
    }

    protected function guessRequisiteType(): string
    {
        $requisite = $this->getRequisite() ?? $this->guessRequisite();
        if (empty($requisite)) {
            throw new \Exception(Yii::t('hipanel:finance', 'Requisite not found'));
        }

        $types = $this->getRequisitesTypes() ?? $this->guessRequisitesTypes();
        foreach ($types as $key => $data) {
            if (preg_match("/{$key}/ui", $requisite->name)) {
                return $key;
            }
        }

        throw new \Exception(Yii::t('hipanel:finance', 'Requisite `{name}` is not available', [
            'name' => $requisite->name,
        ]));
    }

    protected function guessDepositType(): string
    {
        return $this->getChangeType('deposit');
    }

    protected function guessFeeType(): ?string
    {
        return $this->getChangeType('fee');
    }

    protected function guessRequisitesTypes(): ?array
    {
        return $this->getImportData("requisite.types") ?? [];
    }

    protected function guessClientSubstrings(): ?array
    {
        $subtrings = $this->getImportData('client.substrings');
        return empty($subtrings) ? null : $subtrings;
    }

    private function getChangeType(string $name): ?string
    {
        $data = $this->guessRequisitesTypes();

        return $data[$this->requisiteType][$name] ?? null;
    }

    private function getImportData(string $name): array
    {
        $data = Yii::$app->params['module.finance.bill.import'];
        if (empty($data)) {
            return [];
        }

        return $data[$name] ?? [];
    }
}
