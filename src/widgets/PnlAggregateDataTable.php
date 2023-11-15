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
            Html::tag('th', Yii::t('hipanel:finance', 'Date'), ['style' => ['width' => '15%']]),
            Html::tag('th', Yii::t('hipanel:finance', 'Categorized'), ['style' => ['width' => '15%']]),
            Html::tag('th', Yii::t('hipanel:finance', 'Uncategorized')),
            '</tr>',
            '</thead><tbody>',
        ]);
        foreach ($this->aggregateData as $datum) {
            $table .= implode('', [
                Html::beginTag('tr', ['class' => $datum['month']]),
                Html::tag('td', Yii::$app->formatter->asDate($datum['month'], 'MMMM YYYY')),
                Html::tag('td', $datum['categorized']),
                Html::beginTag('td', ['class' => 'uncategorized']),
                Html::beginTag('span',
                    ['style' => 'display: flex; flex-direction: row; justify-content: space-between;']),
                Html::tag('span', $datum['uncategorized']),
                Html::tag('span', null, [
                    'class' => 'glyphicon glyphicon-menu-down text-muted',
                    'style' => ['display' => 'none'],
                    'data' => ['toggle' => 'collapse', 'target' => '#' . $datum['month']],
                ]),
                Html::endTag('span'),
                '
                    <table id="' . $datum['month'] . '" class="table table-striped table-condensed collapse table-bordered" style="margin-top: 1em;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Label</th>
                                <th>Type</th>
                                <th>Object</th>
                            </tr>                
                        </thead>
                        <tbody></tbody>
                    </table>
                ',
                Html::endTag('td'),
                Html::endTag('tr'),
            ]);
        }
        $table .= '</tbody></table>';
        $table .= Html::tag(
            'template',
            '<tr>
                <td class="charge-id" width="10%"><a href="#" target="_blank"></a></td>
                <td class="charge-label" width="40%"></td>
                <td class="charge-type" width="25%"></td>
                <td class="charge-object" width="25%"></td>
            </tr>',
            ['id' => 'charge-info-row']
        );

        return $table;
    }
}
