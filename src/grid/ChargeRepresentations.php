<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hiqdev\higrid\representations\RepresentationCollection;
use Yii;

class ChargeRepresentations extends RepresentationCollection
{
    protected function fillRepresentations()
    {
        $this->representations = array_filter([
            'common' => [
                'label' => Yii::t('hipanel', 'common'),
                'columns' => [
//                    'client',
//                    'seller',
//                    'tariff',
//                    'type_label',
//                    'sum',
//                    'name',
//                    'quantity',
//                    'time',
//                    'is_payed',
//                    'label',
                    'id',
                    'bill_id',
                    'client',
                    'seller',
                    'tariff',
                    'name',
                    'type_label',
                    'root_ftype',
                    'currency',
                    'sum',
                    'quantity',
                    'is_payed',
                    'time',
//                    'description',
                    'label',
                ],
            ],
            'finance' => [
                'label' => Yii::t('hipanel:finance', 'finance'),
                'columns' => [
                    'id',
                    'bill_id',
                    'object_id',
                    'client',
                    'seller',
                    'tariff',
                    'name',
                    'type_label',
                    'time',
                    'sum',
                    'discount_sum',
                    'net_amount',
                    'rate',
                    'eur_amount',
                    'exchange_date',
                    'description',
                ],
            ],
        ]);
    }
}
