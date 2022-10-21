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

class SaleRepresentations extends RepresentationCollection
{
    protected function fillRepresentations()
    {
        $this->representations = array_filter([
            'common' => [
                'label' => Yii::t('hipanel', 'common'),
                'columns' => [
                    'checkbox',
                    'object_type',
                    'object',
                    'seller',
                    'buyer',
                    'tariff',
                    'time',
                    'unsale_time',
                ],
            ],
            'servers' => [
                'label' => Yii::t('hipanel', 'Servers'),
                'columns' => [
                    'checkbox',
                    'object_type',
                    'object',
                    'seller',
                    'buyer',
                    'tariff',
                    'summary',
                    'rack',
                    'tariff_created_at',
                    'tariff_updated_at',
                ],
            ],
        ]);
    }
}
