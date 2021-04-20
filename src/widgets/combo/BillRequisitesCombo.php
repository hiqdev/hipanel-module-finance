<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\combo;

use yii\web\JsExpression;

class BillRequisitesCombo extends RequisitesCombo
{
    /** {@inheritdoc} */
    public $_return = ['id', 'name', 'email', 'organization'];

//    /** {@inheritdoc} */
//    public function getFilter()
//    {
//        return [];
//    }
}
