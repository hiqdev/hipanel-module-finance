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

use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\models\FakeGroupingSale;
use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use Yii;
use yii\helpers\Html;

class SaleGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'tariff' => [
                'format' => 'html',
                'filterAttribute' => 'tariff_like',
                'value' => function ($model) {
                    return Html::a($model->tariff, ['@plan/view', 'id' => $model->tariff_id]);
                },
            ],
            'time' => [
                'format' => ['html'],
                'filter' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function ($model) {
                    $time = Yii::$app->formatter->asDateTime($model->time);
                    return Html::a($time, ['@sale/view', 'id' => $model->id]);
                },
            ],
            'seller' => [
                'class' => ClientColumn::class,
                'idAttribute' => 'seller_id',
                'attribute' => 'seller_id',
                'nameAttribute' => 'seller',
            ],
            'buyer' => [
                'class' => ClientColumn::class,
                'idAttribute' => 'buyer_id',
                'attribute' => 'buyer_id',
                'nameAttribute' => 'buyer',
            ],
            'object_v' => [
                'label' => Yii::t('hipanel:finance:sale', 'Object'),
                'format' => 'html',
                'value' => function ($model) {
                    return $model->object_type . ' ' . LinkToObjectResolver::widget([
                        'model' => $model,
                        'typeAttribute' => 'object_type',
                        'idAttribute' => 'object_id',
                    ]);
                },
            ],
            'object_type' => [
                'label' => Yii::t('hipanel', 'Type'),
                'filterOptions' => ['class' => 'narrow-filter'],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
            'object' => [
                'format' => 'html',
                'filterAttribute' => 'object_inilike',
                'value' => function (Sale $model) {
                    if ($model instanceof FakeSale) {
                        return $model->object;
                    }
                    $html = LinkToObjectResolver::widget([
                        'model' => $model,
                        'typeAttribute' => 'object_type',
                        'idAttribute' => 'object_id',
                    ]);
                    $html .= ' &nbsp; ' . $model->object_label . ' ';

                    return $html;
                },
            ],
            'object_link' => [
                'attribute' => 'object',
                'format' => 'html',
                'filterAttribute' => 'object_like',
                'enableSorting' => false,
                'value' => function (Sale $model) {
                    if ($model instanceof FakeGroupingSale) {
                        return $model->object;
                    }

                    return LinkToObjectResolver::widget([
                        'model' => $model,
                        'typeAttribute' => 'tariff_type',
                        'idAttribute' => 'object_id',
                    ]);
                },
            ],
        ]);
    }
}
