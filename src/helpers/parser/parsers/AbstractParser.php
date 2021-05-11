<?php

declare(strict_types=1);

namespace hipanel\modules\finance\helpers\parser\parsers;

use hipanel\modules\finance\helpers\parser\CanNotParseRowException;
use yii\web\UploadedFile;

abstract class AbstractParser implements ParserInterface
{
    public string $separator = ',';

    /**
     * @var array all rows row, need if fee located on another row
     */
    protected array $rows;

    /**
     * @var array Current parsed row
     */
    protected array $row;

    abstract protected function canParse(): bool;

    public function __construct(UploadedFile $file)
    {
        $this->rows = $this->extractRows($file);
    }

    public function getRows(): array
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

    public function extractRows(UploadedFile $file): array
    {
        $temp = $file->tempName;
        $rows = [];
        if (is_readable($temp)) {
            foreach (file($temp) as $row) {
                $rows[] = str_getcsv($row, $this->separator);
            }
        }

        return $rows;
    }
}
