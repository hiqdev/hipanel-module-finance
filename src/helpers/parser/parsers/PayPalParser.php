<?php
declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser\parsers;

final class PayPalParser extends AbstractParser
{
    protected function canParse(): bool
    {
        if (preg_match('/Reserve (Hold|Release)/ui', $this->row[4])) {
            return false;
        }

        return $this->row[5] === 'Completed' && $this->getClient() !== null;
    }

    public function getSum(): ?float
    {
        return $this->replaceComma($this->row[7]);
    }

    public function getFee(): ?float
    {
        if ($this->row[8] === '0.00') {
            return null;
        }
        return $this->replaceComma($this->row[8]);
    }

    public function getNet(): ?float
    {
        return (float) $this->replaceComma($this->row[9]);
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
        return $this->extractClient(trim($this->row[15]));
    }

    public function getTxn(): ?string
    {
        return $this->row[12] ?? null;
    }

    public function getLabel(): ?string
    {
        return null;
    }

    protected function replaceComma(?string $str): ?float
    {
        return (float) str_replace(",", "", $str);
    }
}
