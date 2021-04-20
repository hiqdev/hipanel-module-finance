<?php

declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser\parsers;

final class CardPayParser extends AbstractParser
{
    public string $separator = ';';

    protected function canParse(): bool
    {
        return $this->getSum() !== null;
    }

    public function getSum(): ?float
    {
        if ($this->row[6] === '') {
            return null;
        }

        return (float)$this->row[6];
    }

    public function getFee(): ?float
    {
        return null;
    }

    public function getCurrency(): ?string
    {
        $currency = '';
        foreach ($this->rows as $row) {
            preg_match('/Currency: (\w*)/', $row[0], $matches);
            if (!empty($matches)) {
                $currency = $matches[1];
            }
            if ($row === $this->row) {
                return $currency;
            }
        }
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
        if (!empty($this->row[0])) {
            return date('Y-m-d H:i:s', strtotime($this->row[0]));
        }

        return null;
    }

    public function getClient(): ?string // todo: add more sophisticated logic
    {
        if (
            str_starts_with($this->row[1], 'Payment for server equipment') &&
            str_starts_with($this->row[1], 'For logistic services ') && !$this->getLabel()
        ) {
            return 'netint';
        }

        return 'fee';
    }

    public function getTxn(): ?string
    {
        return null;
    }

    public function getLabel(): ?string
    {
        if ($this->getClient() === 'netinit') {
            return $this->row[1];
        }

        return $this->row[3];
    }
}
