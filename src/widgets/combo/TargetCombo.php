<?php

namespace hipanel\modules\finance\widgets\combo;

use hipanel\helpers\ArrayHelper;
use hiqdev\combo\Combo;

class TargetCombo extends Combo
{
    public array $targetType = [];

    /** {@inheritdoc} */
    public $type = 'target/name';

    /** {@inheritdoc} */
    public $name = 'target';

    /** {@inheritdoc} */
    public $url = '/finance/target/search';

    /** {@inheritdoc} */
    public $_return = ['id'];

    /** {@inheritdoc} */
    public $_rename = ['text' => 'name'];

    public $_primaryFilter = 'name_ilike';

    /** {@inheritdoc} */
    public function getFilter()
    {
        return ArrayHelper::merge(parent::getFilter(), [
            'type_in'  => ['format' => $this->targetType],
            'limit' => ['format' => '50'],
        ]);
    }
}
