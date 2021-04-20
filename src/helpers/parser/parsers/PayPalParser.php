<?php
declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser\parsers;

final class PayPalParser extends AbstractParser
{
    protected function canParse(): bool
    {
        return $this->row[5] === 'Completed' && $this->getClient() !== null;
    }

    public function getSum(): ?float
    {
        return (float)$this->row[7];
    }

    public function getFee(): ?float
    {
        return $this->row[8] !== '0.00' ? (float)$this->row[8] : null;
    }

    public function getCurrency(): ?string
    {
        return $this->row[6] ?? null;
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
        $t = sprintf('%s %s', str_replace('/', '.', $this->row[0]), $this->row[1]);
        if (is_int(strtotime($t))) {
            return date('Y-m-d H:i:s', strtotime($t));
        }

        return null;
    }

    public function getClient(): ?string
    {
        preg_match('/^AH RCP : (\w*)/', trim($this->row[15]), $matches);
        if (empty($matches)) {
            return null;
        }

        return $matches[1];
    }

    public function getTxn(): ?string
    {
        return $this->row[12] ?? null;
    }

    public function getLabel(): ?string
    {
        return null;
    }
}
