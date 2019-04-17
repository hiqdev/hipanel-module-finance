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

use hipanel\models\Ref;
use yii\base\Model;

class Currency extends Model
{
    /**
     * @return array list of possible currencies
     */
    public static function list(): array
    {
        return Ref::getList('type,currency', 'hipanel', ['orderby' => 'no_asc']);
    }
}
