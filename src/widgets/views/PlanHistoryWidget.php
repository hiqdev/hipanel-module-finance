<?php


use hipanel\modules\finance\controllers\TariffController;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\PlanHistory;
use hipanel\modules\finance\widgets\PlanHistoryWidget;
use hipanel\widgets\ArraySpoiler;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var PlanHistoryWidget $widget
 * @var array $models
 * @var Plan $model
 * @var PlanHistory $history
 */

?>

<div class="row-md-12">
    <div class="col-md-12"><?= Yii::t('hipanel:finance', 'Tarriffs history') ?></div>

    <?php foreach ($models as $date => $dateModels): ?>
        <?= ArraySpoiler::widget([
            'data' => $dateModels,
            'delimiter' => '<hr />',
            'visibleCount' => 0,
            'formatter' => function ($model, $idx) {
                /** @var PlanHistory $model */
                return "
                    <div>
                        <div>
                            Name: {$model->name}               
                        </div>
                        <div>
                            Old price: {$model->old_price}               
                        </div>
                        <div>
                            Type: {$model->type_name}               
                        </div>
                    </div>
                ";
            },
            'button' => [
                'label' => $date,
                'tag' => 'button',
                'type' => 'button',
                'class' => 'btn btn-xs btn-flat',
                'style' => 'font-size: 10px',
                'popoverOptions' => [
                    'html' => true,
                    'placement' => 'bottom',
                    'title' => Html::a(Yii::t('hipanel:finance', 'Show detailed history'), TariffController::getSearchUrl(['tariff_id' => 'id'])),
                    'template' => '
                    <div class="popover" role="tooltip">
                        <div class="arrow"></div>
                        <h3 class="popover-title"></h3>
                        <div class="popover-content" style="min-width: 15rem; height: 15rem; overflow-x: scroll;"></div>
                    </div>
                ',
                ],
            ],
        ]); ?>
    <?php endforeach; ?>
</div>
