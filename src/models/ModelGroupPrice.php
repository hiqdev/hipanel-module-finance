<?php

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;

/**
 * Class ModelGroupPrice
 *
 * @property int[] $subprices
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ModelGroupPrice extends Price
{
    use ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['subprices'], 'each', 'rule' => ['number']]
        ]);
    }
}
