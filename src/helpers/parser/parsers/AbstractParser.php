<?php

declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser\parsers;

use hipanel\modules\finance\helpers\parser\CanNotParseRowException;

abstract class AbstractParser implements ParserInterface
{
    /**
     * @var array all rows row, need if fee located on another row
     */
    public array $rows;

    /**
     * @var array Current parsed row
     */
    protected array $row;

    abstract protected function canParse(): bool;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function getParsedRows(): array
    {
        $rows = [];
        foreach ($this->rows as $row) {
            $this->row = $row;
            if ($this->canParse()) {
                $rows[] = clone $this;
            }
        }

        return $rows;
    }
}
