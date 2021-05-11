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
        $fees = array_filter($this->rows, fn($row): bool => $row[4] === 'Transfer Fee' && $row[3] === $this->row[3]);
        if (!empty($fees)) {
            $fee = reset($fees);

            return $fee[7] > 0 ? (float)$fee[7] : null;
        }

        return null;
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
        $str = $this->row[2];
        preg_match('/( : (\w*))/', $str, $matches);
        if (empty($matches)) {
            return null;
        }

        return $matches[2];
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
