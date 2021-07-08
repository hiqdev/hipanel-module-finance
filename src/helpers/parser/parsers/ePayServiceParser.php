<?php

declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser\parsers;

final class ePayServiceParser extends AbstractParser
{
    protected function canParse(): bool
    {
        return $this->getClient() !== null;
    }

    public function getSum(): ?float
    {
        return (float)$this->row[4];
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
        return $this->row[0];
    }

    public function getClient(): ?string
    {
        return $this->findClient($this->row[3]);
    }

    public function getTxn(): ?string
    {
        return null;
    }

    public function getFee(): ?float
    {
        return null;
    }

    public function getNet(): ?float
    {
        return $this->getSum();
    }

    public function getLabel(): ?string
    {
        return null;
    }
}
