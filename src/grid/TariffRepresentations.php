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

class TariffRepresentations extends RepresentationCollection
{
    protected function fillRepresentations()
    {
        $columns = array_filter([
            (Yii::$app->user->can('plan.update') || Yii::$app->user->can('plan.delete')) ? 'checkbox' : null,
            'tariff',
            'used',
            'type',
            'client_id',
        ]);
        $this->representations = array_filter([
            'common' => [
                'label' => Yii::t('hipanel', 'common'),
                'columns' => $columns,
            ],
        ]);
    }
}
