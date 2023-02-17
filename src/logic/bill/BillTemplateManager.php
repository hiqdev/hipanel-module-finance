<?php

declare(strict_types=1);

namespace hipanel\modules\finance\logic\bill;

use hipanel\modules\finance\actions\BillManagementAction;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\logic\bill\template\Template;
use hipanel\modules\finance\logic\bill\template\TemplateInterface;

readonly class BillTemplateManager
{
    private ?Template $template;

    public function __construct(private BillManagementAction $action)
    {
        $template = $this->action->request->get('template');
        $this->template = $template ? Template::tryFrom($template) : null;
    }

    public function isAcceptable(): bool
    {
        return $this->action->request->isGet && $this->template !== null;
    }

    public function getTemplatedForm(): array
    {
        if ($this->template === null) {
            return [new BillForm(['scenario' => $this->action->scenario])];
        }
        $form = $this->template->create()->build();
        $form->scenario = $this->action->scenario;

        return [$form];
    }

    public function getTemplate(): ?TemplateInterface
    {
        return $this->template?->create();

    }
}
