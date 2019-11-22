<?php


use hipanel\modules\finance\controllers\TariffController;
use hipanel\widgets\ArraySpoiler;
use yii\bootstrap\Html;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\widgets\PlanHistoryWidget $widget
 * @var array $models
 */

?>

<div class="row-md-12">
    <div class="col-md-12"><?= Yii::t('hipanel:finance', 'Tarriffs history') ?></div>

    <?= ArraySpoiler::widget([
        'data' => $models,
        'delimiter' => '<br />',
        'visibleCount' => 0,
        'formatter' => function ($model, $idx) {
            return 'some';
//        return Html::a($part->title, Url::toRoute(['@part/view', 'id' => $part->id]), [
//            'class' => 'text-bold',
//            'target' => '_blank',
//        ]);
        },
        'button' => [
            'label' => count($models),
            'tag' => 'button',
            'type' => 'button',
            'class' => 'btn btn-xs btn-flat',
            'style' => 'font-size: 10px',
            'popoverOptions' => [
                'html' => true,
                'placement' => 'bottom',
                'title' => Html::a(Yii::t('hipanel:finance', 'Show all items'), TariffController::getSearchUrl(['tariff_id' => 'id'])),
                'template' => '
                <div class="popover" role="tooltip">
                    <div class="arrow"></div>
                    <h3 class="popover-title"></h3>
                    <div class="popover-content" style="min-width: 15rem; height: 15rem; overflow-x: scroll;"></div>
                </div>
            ',
            ],
        ],
    ]);
    ?>
</div>
