<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\assets\PriceEstimator;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\RegionalPrice;
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
        $this->registerClientScript();
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        if (!$model instanceof RegionalPrice) {
            return $this->renderEstimateBox($model);
        }

        // the estimator matches a row by the price's own type, so every region gets its own box
        $rows = [];
        foreach ($model->members as $region => $price) {
            $rows[] = Html::tag('div', $this->grid->renderRegionBadge($region) . $this->renderEstimateBox($price), [
                'style' => $this->grid->regionBlockStyle($rows === []) . PriceGridView::REGION_ROW_STYLE,
            ]);
        }

        return implode('', $rows);
    }

    private function renderEstimateBox(Price $model): string
    {
        $fields = Html::hiddenInput("[{$model->id}]object_id", $model->object_id, ['ref' => 'object_id']);
        $fields .= Html::hiddenInput("[{$model->id}]type", $model->type, ['ref' => 'type']);

        return Html::tag('span', Html::tag('span', Html::tag('i', $fields, [
            'class' => 'fa fa-refresh fa-spin fa-fw',
            'data' => ['id' => $model->id],
        ]), ['class' => 'price-estimates']), ['class' => 'price-item', 'data' => [
            'id' => $model->id,
            'currency' => mb_strtoupper($model->currency),
        ]]);
    }

    private function registerClientScript()
    {
        $calculateValueUrl = Url::toRoute(['@plan/calculate-values', 'planId' => $this->planId]);
        $view = Yii::$app->view;
        $view->registerAssetBundle(PriceEstimator::class);
        $view->registerJs(/** @lang ECMAScript 6 */
            "
        ;(function ($, window, document, undefined) {
            let Estimator = $('#bulk-plan').priceEstimator({
                url: '$calculateValueUrl',
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
