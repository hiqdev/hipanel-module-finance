<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;
use hipanel\grid\RefColumn;
use hipanel\helpers\Url;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\menus\PlanActionsMenu;
use hiqdev\yii2\menus\grid\MenuColumn;
use yii\helpers\Html;
use Yii;

class PlanGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'client' => [
                'class' => ClientColumn::class,
            ],
            'name' => [
                'attribute' => 'name',
                'filterAttribute' => 'name_ilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'class' => MainColumn::class,
                'note' => 'note',
                'noteOptions' => [
                    'url' => Url::to(['@plan/set-note']),
                ],
                'badges' => function ($model) {
                    return $this->prepareBadges($model);
                },
            ],
            'simple_name' => [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function ($model) {
                    return sprintf('%s %s', $model->name, $this->prepareBadges($model));
                },
            ],
            'state' => [
                'attribute' => 'state',
                'class' => RefColumn::class,
                'filterAttribute' => 'state',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'html',
                'gtype' => 'state,tariff',
            ],
            'type' => [
                'attribute' => 'type',
                'class' => RefColumn::class,
                'filterAttribute' => 'type',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'html',
                'gtype' => 'type,tariff',
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => PlanActionsMenu::class,
            ],
            'monthly' => [
                'attribute' => 'monthly',
                'contentOptions' => ['id' => 'plan-monthly-value'],
            ],
        ]);
    }

    protected function prepareBadges($model)
    {
        $localization = Yii::t('hipanel.finance.plan', 'Grouping');
        if ($model->is_grouping) {
            return Html::tag('span', $localization, ['class' => 'label bg-olive', 'style' => 'float:right']);
        }
        return '';
    }
}
