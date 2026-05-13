<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets\FinanceDocumentsBox;

use yii\web\Application;

interface FinanceDocumentsDataSource
{
    /** Name of the JS function to call on window.FinanceDocumentsBox (e.g. 'mount' or 'mountDocumentsOnly'). */
    public function getMountFunctionName(): string;

    /** Returns a JSON-encoded props string ready to pass to the JS mount call. */
    public function buildJsProps(Application $app): string;

    public function hasDocuments(): bool;
}
