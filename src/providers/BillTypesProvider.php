<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\providers;

use hipanel\helpers\ArrayHelper;
use hipanel\models\Ref;
use hipanel\modules\finance\models\Bill;
use yii\base\Application;

/**
 * Class BillTypesProvider.
 */
class BillTypesProvider
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var bool
     */
    private $showUnusedTypes = false;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns key-value list of bill types.
     * `key` - type name
     * `value` - type label (translated).
     * @return array
     */
    public function getTypesList()
    {
        return ArrayHelper::map($this->getTypes(), 'name', 'label');
    }

    /**
     * Returns array of types.
     * When user can not support, filters out unused types.
     * @return Ref[]
     */
    public function getTypes()
    {
        $options = ['select' => 'full', 'orderby' => 'name_asc', 'with_hierarchy' => true];
        $types = Ref::findCached('type,bill', 'hipanel:finance', $options);

        if (!$this->app->user->can('support')) {
            $types = $this->removeUnusedTypes($types);
        }

        return $types;
    }

    /**
     * @param Ref[] $types
     * @return Ref[]
     */
    private function removeUnusedTypes($types)
    {
        if ($this->showUnusedTypes === true) {
            return $types;
        }
        $ids = $this->app->cache->getOrSet([__METHOD__, $this->app->user->id], function () use ($types) {
            return ArrayHelper::getColumn(Bill::perform('get-used-types', [], ['batch' => true]), 'id');
        }, 3600);

        return array_filter($types, function ($model) use ($ids) {
            return in_array($model->id, $ids, true);
        });
    }

    /**
     * @return array
     */
    public function getGroupedList()
    {
        $billTypes = [];
        $billGroupLabels = [];

        $types = $this->getTypesList();

        foreach ($types as $key => $title) {
            list($type, $name) = explode(',', $key);

            if (!isset($billTypes[$type])) {
                $billTypes[$type] = [];
                $billGroupLabels[$type] = ['label' => $title];
            }

            if (isset($name)) {
                foreach ($types as $k => $t) {
                    if (strpos($k, $type . ',') === 0) {
                        $billTypes[$type][$k] = $t;
                    }
                }
            }
        }

        return [$billTypes, $billGroupLabels];
    }

    public function alwaysShowUnusedTypes(): void
    {
        $this->showUnusedTypes = true;
    }
}
