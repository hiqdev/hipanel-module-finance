<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class PnlAggregateDataTable extends Widget
{
    /**
     * @var array{
     *     month: string,
     *     categorized: int,
     *     uncategorized: int,
     *     chargesInfo: string[]
     * }
     */
    public array $aggregateData = [];

    public function run(): string
    {
        if (empty($this->aggregateData)) {
            return '';
        }
        $table = implode('', [
            '<table class="table"><thead>',
            '<tr>',
            Html::tag('th', Yii::t('hipanel:finance', 'Date')),
            Html::tag('th', Yii::t('hipanel:finance', 'Categorized')),
            Html::tag('th', Yii::t('hipanel:finance', 'Uncategorized')),
            '</tr>',
            '</thead><tbody>',
        ]);
        foreach ($this->aggregateData as $datum) {
            $table .= implode('', [
                Html::beginTag('tr', ['id' => $datum['month']]),
                Html::tag('td', Yii::$app->formatter->asDate($datum['month'], 'MMMM YYYY')),
                Html::tag('td', $datum['categorized']),
                Html::tag('td',
                    "<span>$datum[uncategorized]</span><span class=\"glyphicon glyphicon-menu-right text-muted\" style='display: none;'></span>",
                    [
                        'class' => 'uncategorized',
                        'data-toggle' => 'popover',
                        'style' => 'display: flex; flex-direction: row; justify-content: space-between;'
                    ]),
                Html::endTag('tr'),
            ]);
        }
        $table .= '</tbody></table>';

        return $table;
    }
}
