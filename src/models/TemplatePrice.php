<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;

/**
 * Class TemplatePrice.
 *
 * @property int[] $subprices
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TemplatePrice extends Price
{
    use ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['subprices'], 'each', 'rule' => ['number']],
            [['rate'], 'number', 'min' => 0],
        ]);
    }
}
