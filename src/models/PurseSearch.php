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

use hipanel\base\SearchModelTrait;
use hipanel\helpers\ArrayHelper;
use Yii;

class PurseSearch extends Purse
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
        rules as defaultRules;
    }

    public function rules()
    {
        return ArrayHelper::merge($this->defaultRules(), [
        ]);
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
        ]);
    }

    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
        ]);
    }
}
