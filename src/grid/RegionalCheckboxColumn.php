<?php declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\grid\CheckboxColumn;
use hipanel\modules\finance\models\RegionalPrice;
use yii\helpers\Html;

/**
 * Selecting a grouped regional row selects every regional price it stands for.
 */
class RegionalCheckboxColumn extends CheckboxColumn
{
    protected function renderDataCellContent($model, $key, $index)
    {
        if (!$model instanceof RegionalPrice) {
            return parent::renderDataCellContent($model, $key, $index);
        }

        $ids = $model->memberIds();
        $options = $this->checkboxOptions instanceof \Closure
            ? ($this->checkboxOptions)($model, $key, $index, $this)
            : $this->checkboxOptions;
        $visible = Html::checkbox($this->name, false, $options + [
            'value' => array_shift($ids),
            'onclick' => "$(this).closest('td').find('.regional-member').prop('checked', this.checked)",
        ]);
        $hidden = array_map(
            fn($id) => Html::checkbox($this->name, false, ['value' => $id, 'class' => 'regional-member grid-checkbox option-input', 'style' => 'display:none']),
            $ids,
        );

        return $visible . implode('', $hidden);
    }
}
