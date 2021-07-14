<?php

declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser\parsers;

class PaxumParser extends AbstractParser
{
    protected function canParse(): bool
    {
        return $this->row[4] === 'Transfer' && $this->getClient();
    }

    public function getSum(): ?float
    {
        return (float)$this->row[7];
    }

    public function getFee(): ?float
    {
        $fees = array_filter($this->rows, function($row): bool {
            return $row[4] === 'Transfer Fee'
                && (
                    $row[3] === $this->row[3]
                ||  $row[0] === $this->row[0]
            );
        });
        if (!empty($fees)) {
            $fee = reset($fees);

            return $fee[6] > 0 ? (float)"-{$fee[6]}" : null;
        }

        return null;
    }

    public function getNet(): ?float
    {
        return $this->getSum() + $this->getFee();
    }

    public function getCurrency(): ?string
    {
        return 'usd';
    }

    public function getQuantity(): ?int
    {
        return 1;
    }

    public function getUnit(): ?string
    {
        return 'items';
    }

    public function getTime(): ?string
    {
        return $this->row[1];
    }

    public function getClient(): ?string
    {
        return $this->extractClient($this->row[2]);
    }

    public function getTxn(): ?string
    {
        return $this->row[0];
    }

    public function getLabel(): ?string
    {
        return null;
    }
}
