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
                url: '${calculateValueUrl}',
                estimatePlan: true,
                rowSelector: '.price-item',
                totalCellSelector: '#plan-monthly-value',
                totalPerObjectSelector: '.total-per-object',
            });
            Estimator.update();
        })(jQuery, window, document);
");
    }

    private function findPlanId()
    {
        return Yii::$app->request->get('id');
    }
}
