<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\PriceHistory;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class PriceHistoryWidget
 * @package hipanel\modules\finance\widgets
 */
class PriceHistoryWidget extends Widget
{
    /**
     * @var Plan
     */
    public $model;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->registerJsScript();

        return $this->render('PriceHistoryWidget', [
            'collapseItems' => $this->renderCollapseItems(),
            'widget' => $this,
        ]);
    }

    /**
     * @return string[]
     */
    private function getSortedTimesArray(): array
    {
        $dates = array_unique(ArrayHelper::getColumn($this->model->priceHistory, function (PriceHistory $el): string {
            return $el->time;
        }));
        rsort($dates);

        return $dates;
    }

    /**
     * @return string[]
     */
    private function renderCollapseItems(): array
    {
        $res = [];
        foreach ($this->getSortedTimesArray() as $date) {
            $res[] = [
                'label' => $date,
                'content' => '',
                'options' => [
                    'id' => $date,
                ],
            ];
        }

        return array_filter([
            'items' => $res,
        ]);
    }

    private function registerJsScript(): void
    {
        $calculateValueUrl = Url::toRoute(['@plan/get-plan-history', 'plan_id' => $this->model->id]);

        $this->view->registerJs(<<<JS
(function ($, window, document, undefined) {
    $('.collapse-toggle').on('click', function() {
        $.ajax({
            method: 'post',
            url: `$calculateValueUrl&date=\${\$(this).text()}`,
            success: (res) => {
                const collapsBody = $(this).attr('href') + ' .panel-body';
                $(collapsBody).html(res);

                //Initialize ArraySpoiler onclick
                $('a[id*=w][class*=badge]').popover({"placement":"bottom","html":true}).on('show.bs.popover', function(e) {
                    $('[data-popover-group="main"]').not(e.target).popover('hide');
                });
            },
            error: function (xhr) {
                hipanel.notify.error(xhr.statusText);
            }
        });
    });
})(jQuery, window, document);
JS
        );
    }
}
