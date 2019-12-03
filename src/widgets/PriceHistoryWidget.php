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
use hipanel\modules\finance\grid\PriceGridView;
use hipanel\modules\finance\grid\SalesInPlanGridView;
use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\models\Plan;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

class PriceHistoryWidget extends Widget
{
    /**
     * @var Plan
     */
    public $model;

    public function run()
    {
        $planHistory = ArrayHelper::index($this->model->priceHistory, 'id', [function ($el) {
            return $el->time;
        }]);

        $this->registerJsScript();

        return $this->render('PriceHistoryWidget', [
            'collapseItems' => $this->renderCollapseItems($planHistory),
            'widget' => $this,
        ]);
    }

    private function renderCollapseItems($planHistory)
    {
        $res = [];
        foreach ($planHistory as $date => $models) {
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

    private function registerJsScript()
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
