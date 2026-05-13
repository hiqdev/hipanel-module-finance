<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets\FinanceDocumentsBox;

interface FinanceDocumentsDataSource
{
    /** Name of the JS function to call on window.FinanceDocumentsBox (e.g. 'mount' or 'mountDocumentsOnly'). */
    public function getMountFunctionName(): string;

    /**
     * Builds props for the JS mount call.
     *
     * Returns a JSON-encoded string by default, or a raw props array when `$raw` is `true`.
     *
     * @param bool $raw Whether to return raw props instead of JSON.
     * @return string|array JSON-encoded props string or raw props array.
     */
    public function buildJsProps(bool $raw = false): string|array;

    public function hasDocuments(): bool;
}
