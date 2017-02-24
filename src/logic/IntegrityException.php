<?php

namespace hipanel\modules\finance\logic;

use yii\base\Exception;

/**
 * Class IntegrityException represents exception caused because of
 * logical integrity constraints violation.
 */
class IntegrityException extends Exception
{
    public function getName()
    {
        return 'Integrity constraint violation';
    }
}
