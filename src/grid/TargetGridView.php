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
            ],
            'type' => [
                'filter' => $this->filterModel?->types,
                'filterInputOptions' => ['class' => 'form-control'],
                'value' => static fn(Target $target): string => Yii::t('hipanel:finance', $target->type),
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => TargetActionsMenu::class,
            ],
        ]);
    }
}
