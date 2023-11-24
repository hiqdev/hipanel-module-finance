<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2021, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hiqdev\higrid\representations\RepresentationCollection;
use Yii;

class PnlRepresentations extends RepresentationCollection
{
    protected function fillRepresentations()
    {
        $this->representations = array_filter([
            'common' => [
                'label' => Yii::t('hipanel', 'common'),
                'columns' => [
                    'charge_id',
                    'seller',
                    'type',
                    'month',
                    'currency',
                    'sum',
                    'charge_sum',
                    'discount_sum',
                    'eur_amount',
                    'rate',
                    'exchange_date',
                    'charge_date',
                ],
            ],
        ]);
    }
}
