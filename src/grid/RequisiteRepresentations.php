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

class RequisiteRepresentations extends RepresentationCollection
{
    protected function fillRepresentations()
    {
        $this->representations = array_filter([
            'common' => [
                'label' => Yii::t('hipanel', 'Common'),
                'columns' => array_filter([
                    'checkbox',
                    'client_like',
                    'name', 'actions', 'serie', 'requisites', 'templates'
                ]),
            ],
            'balances' => [
                'label' => Yii::t('hipanel:finance', 'Balances'),
                'columns' => array_filter([
                    'checkbox',
                    'client_like',
                    'name',
                    'actions',
                    'serie',
                    'usd',
                    'eur',
                    'uah',
                    'rub',
                    'pln',
                    'btc',
                    'sgd',
                    'gbp',
                ]),
            ],
            'balance' => [
                'label' => Yii::t('hipanel', 'Balance'),
                'columns' => array_filter([
                    'checkbox',
                    'client_like',
                    'name',
                    'actions',
                    'serie',
                    'balance',
                    'debit',
                    'credit',
                ]),
            ],
        ]);
    }

}
