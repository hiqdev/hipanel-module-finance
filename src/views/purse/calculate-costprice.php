<?php
/** @var array $statisticByTypes */
use hipanel\modules\finance\widgets\StatisticTableGenerator;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('hipanel:finance', 'Calculate costprice');
$this->params['subtitle'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:document', 'System tools'), 'url' => ['system-tools']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-widget">
            <div class="box-header with-border">
                <?php $form = ActiveForm::begin() ?>
                <div class="box-tools pull-left">
                    <div class="loading btn-box-tool" style="display: inline-block;">
                        <i class="fa fa-refresh fa-spin"></i> <?= Yii::t('hipanel', 'loading...') ?>
                    </div>
                    <button
                        type="submit"
                        class="btn btn-default btn-box-tool"
                        formmethod="POST"
                        formaction="/finance/purse/calculate-costprice"
                    >
                        <?= Yii::t('hipanel:document', 'Calculate costprice') ?>
                    </button>
                </div>
                <?php ActiveForm::end() ?>
            </div>
            <div class="box-body no-padding">
                <?= StatisticTableGenerator::widget(['type' => 'costprice', 'statistic' => ['statistic']]) ?>
            </div>
            <div class="overlay" style="display: none;">
            </div>
        </div>
    </div>
</div>
