<?php

namespace hipanel\modules\finance\widgets\combo;

use hiqdev\combo\Combo;

class TargetCombo extends Combo
{
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
}
