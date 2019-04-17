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

/**
 * Class Tariff.
 * @property resource[]|DomainResource[]|ServerResource[] $resources
 */
class TariffProfileSearch extends TariffProfile
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['id', 'ids'], 'safe', 'on' => ['search']],
        ]);
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
        ]);
    }
}
