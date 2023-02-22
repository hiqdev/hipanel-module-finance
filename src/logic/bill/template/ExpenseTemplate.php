<?php

declare(strict_types=1);

namespace hipanel\modules\finance\logic\bill\template;

use hipanel\modules\finance\forms\BillForm;

final class ExpenseTemplate implements TemplateInterface
{
    public function build(): BillForm
    {
        return new BillForm(['label' => 'TEST TEMPLATED LABEL']);
    }

    public function allowedTypes(): array
    {
        return ['expense'];
    }
}
