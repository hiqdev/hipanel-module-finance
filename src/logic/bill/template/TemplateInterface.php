<?php

declare(strict_types=1);

namespace hipanel\modules\finance\logic\bill\template;

use hipanel\modules\finance\forms\BillForm;

interface TemplateInterface
{
    public function build(): BillForm;

    public function allowedTypes(): array;
}
