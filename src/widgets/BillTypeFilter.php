<?php /**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\providers\BillTypesProvider;
use hipanel\widgets\RefFilter;
use Yii;

class BillTypeFilter extends RefFilter
{
    public array $billTypeList = [];

    protected function getRefs()
    {
        /** @var BillTypesProvider $provider */
        $provider = Yii::createObject(BillTypesProvider::class);

        return $provider->getTypes();
    }

    /**
     * Prefixes bill types (not categories of types) with `--` string.
     *
     * @param array $types
     * @param string $prefix
     * @return array
     */
    private function prefixBillTypes($types, $prefix = '-- ')
    {
        foreach ($types as $key => $title) {
            if (count(explode(',', $key)) > 1) {
                $types[$key] = $prefix . $title;
            }
        }

        return $types;
    }

    protected function renderInput(): string
    {
        return BillTypeVueTreeSelect::widget([
            'attribute' => $this->attribute,
            'model' => $this->model,
            'billTypes' => !empty($this->billTypeList) ? $this->billTypeList : $this->getRefs(),
            'deprecatedTypes' => Yii::$app->params['module.finance.bill.types']['deprecated.types'],
            'behavior' => TreeSelectBehavior::Deprecated,
        ]);
    }
}
