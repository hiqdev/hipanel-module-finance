<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\assets\PriceEstimator;
use Yii;
use yii\grid\Column;
use yii\helpers\Html;
use yii\helpers\Url;

class ValueColumn extends Column
{
    public $attribute = 'value';

    /**
     * @var integer
     */
    private $planId;

    public function init()
    {
        parent::init();
        $this->header = Yii::t('hipanel.finance.plan', 'Estimated value');
        $this->planId = $this->findPlanId();
        $this->regesterClientScript();
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $fields = Html::hiddenInput("[{$model->id}]object_id", $model->object_id);
        $fields .= Html::hiddenInput("[{$model->id}]type", $model->type);

        return Html::tag('span', Html::tag('span', Html::tag('i', $fields, [
            'class' => 'fa fa-refresh fa-spin fa-fw',
            'data' => ['id' => $model->id],
        ]), ['class' => 'price-estimates']), ['class' => 'price-item', 'data' => ['id' => $model->id]]);
    }

    private function regesterClientScript()
    {
        $calculateValueUrl = Url::toRoute(['@plan/calculate-values', 'planId' => $this->planId]);
        $view = Yii::$app->view;
        $view->registerAssetBundle(PriceEstimator::class);
        $view->registerJs(/** @lang ECMAScript 6 */
            "
        ;(function ($, window, document, undefined) {
            let Estimator = $('#bulk-plan').priceEstimator({
                rowSelector: '.price-item',
            });
            function drawPlanTotal(rows) {
                let totalCell = $('#plan-monthly-value'), sum = '&mdash;';
                totalCell.html('');
                Object.keys(rows).forEach(period => {
                    let estimate = rows[period];
                    if (estimate) {
                        sum = estimate['sumFormatted'];
                    }

                    if (totalCell.html().length === 0) {
                        totalCell.append($('<strong>').attr({title: period}).html(sum));
                    } else {
                        totalCell.append('&nbsp; ');
                        totalCell.append($('<i>').attr({title: period}).html(sum));
                    }
                });
            }
            function drawDynamicQuantity(rows) {
                let firstPeriod = Object.keys(rows)[0];
                let period = rows[firstPeriod];
                if (period.targets) {
                    Object.keys(period.targets).forEach(object_id => {
                        let objectActions = period.targets[object_id];

                        Object.keys(objectActions).forEach(type => {
                            let row = Estimator.matchPriceRow(object_id, type);
                            if (row) {
                                let dynamicQuantity = row.parents('tr[data-key]').find('[data-dynamic-quantity]');
                                if (dynamicQuantity.length) {
                                    dynamicQuantity.text(objectActions[type].quantity);
                                }
                            }
                        });
                    });
                }
            }
            $.ajax({
                method: 'post',
                url: '{$calculateValueUrl}',
                success: json => {
                    drawDynamicQuantity(json);
                    Object.keys(json).forEach(period => {
                        Estimator.rememberEstimates(period, json[period].targets);
                        Estimator.rememberCurrency(period, json[period].sumFormatted);
                    });
                    Estimator.drawEstimates();
                    Estimator.drawTotalPerServer();
                    drawPlanTotal(json);
                },
                error: xhr => {
                    hipanel.notify.error(xhr.statusText);
                    $('.price-estimates').text('--');
                }
            });
        })(jQuery, window, document);
");
    }

    private function findPlanId()
    {
        return Yii::$app->request->get('id');
    }
}
