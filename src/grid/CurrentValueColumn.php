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

class CurrentValueColumn extends Column
{
    public $attribute = 'current-value';

    /**
     * @var integer
     */
    private $planId;

    public function init()
    {
        parent::init();
        $this->header = Yii::t('hipanel.finance.price', 'Estimate value');
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
        $calculateCurrentValueUrl = Url::toRoute(['@plan/calculate-current-value', 'planId' => $this->planId]);
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
                        totalCell.append('; ');
                        totalCell.append($('<i>').attr({title: period}).html(sum));
                    }
                });
            }
            $.ajax({
                method: 'post',
                url: '{$calculateCurrentValueUrl}',
                success: json => {
                    Object.keys(json).forEach(period => Estimator.rememberEstimates(period, json[period].targets));
                    Estimator.drawEstimates();
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
