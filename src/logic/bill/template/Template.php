<?php

declare(strict_types=1);

namespace hipanel\modules\finance\logic\bill\template;

enum Template: string
{
    case Expense = 'expense';

    public function create(): ?TemplateInterface
    {
        return match ($this) {
            self::Expense => new ExpenseTemplate(),
        };
    }
}
