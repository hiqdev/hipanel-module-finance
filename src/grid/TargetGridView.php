<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\MainColumn;
use hipanel\modules\finance\menus\TargetActionsMenu;
use hipanel\modules\finance\models\Target;
use hiqdev\yii2\menus\grid\MenuColumn;
use hipanel\modules\client\grid\ContactGridView;
use Yii;

class TargetGridView extends ContactGridView
{
    public function columns(): array
    {
        return array_merge(parent::columns(), [
            'name' => [
                'class' => MainColumn::class,
                'exportedColumns' => ['tags', 'name'],
                'filterAttribute' => 'name_like',
            ],
            'state' => [
                'filter' => $this->filterModel?->states,
                'filterInputOptions' => ['prompt' => '--', 'class' => 'form-control'],
            ],
            'type' => [
                'filter' => $this->filterModel?->types,
                'filterInputOptions' => ['prompt' => '--', 'class' => 'form-control'],
                'value' => static fn(Target $target): string => Yii::t('hipanel:finance', $target->type),
            ],
            'remoteid' => [
                'filterAttribute' => 'remoteid_like',
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => TargetActionsMenu::class,
            ],
        ]);
    }
}
