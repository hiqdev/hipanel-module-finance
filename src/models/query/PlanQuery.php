<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\query;

use hipanel\modules\finance\models\Plan;
use hiqdev\hiart\ActiveQuery;

class PlanQuery extends \hiqdev\hiart\ActiveQuery
{
    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param null $db
     * @return Plan|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @param null $db
     * @return Plan[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    public function withPrices()
    {
        return $this->with([
            'prices' => function (PriceQuery $query) {
                $query
                    ->withMainObject()
                    ->withFormulaLines();
            },
        ]);
    }

    public function joinWithPrices()
    {
        return $this->joinWith([
            'prices' => function (ActiveQuery $query) {
                $query
                    ->addSelect('main_object_id')
                    ->joinWith('object')
                    ->limit(-1);
            },
        ]);
    }

    /**
     * @return $this
     */
    public function withSales(?array $filter = null)
    {
        $this
            ->joinWith('sales')
            ->andWhere(['states' => ['ok', 'deleted']]);
        if ($filter && isset($filter['saleObjectInilike'])) {
            $this->andWhere(['sale_object_inilike' => $filter['saleObjectInilike']]);
        }
        if ($filter && isset($filter['saleBuyerId'])) {
            $this->andWhere(['sale_buyer_id' => $filter['saleBuyerId']]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function withPriceHistory()
    {
        $this
            ->joinWith([
                'priceHistory' => function (ActiveQuery $query): void {
                    $query
                        ->addSelect('main_object_id')
                        ->joinWith('object');
                },
            ])
            ->andWhere(['priceHistory' => true]);

        return $this;
    }
}
