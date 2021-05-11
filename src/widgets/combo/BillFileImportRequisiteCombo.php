<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\combo;

class BillFileImportRequisiteCombo extends BillRequisitesCombo
{
    /** {@inheritdoc} */
    public $hasId = true;

    /** {@inheritdoc} */
    public function getFilter()
    {
        return [
            'name_in' => ['format' => $this->model->getRequisiteNames()],
        ];
    }
}
