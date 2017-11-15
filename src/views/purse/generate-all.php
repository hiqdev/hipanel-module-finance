<?php

/** @var array $statisticByTypes */

use hipanel\modules\finance\widgets\StatisticTableGenerator;

$this->title = Yii::t('hipanel:finance', 'Generate documents');
$this->params['subtitle'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:document', 'Documents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <?php if (empty($statisticByTypes)) : ?>
        <div class="col-md-12">
            <p class="text-center bg-danger" style="padding: 1em;">
                <?= Yii::t('hipanel:document', 'Data not found.') ?>
            </p>
        </div>
    <?php else : ?>
        <?php foreach ($statisticByTypes as $type => $statistic) : ?>
            <div class="col-md-6">
                <div class="box box-widget">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= Yii::t('hipanel:document', ucfirst($type)) ?></h3>
                        <div class="box-tools pull-right">
                            <div class="loading btn-box-tool" style="display: inline-block;">
                                <i class="fa fa-refresh fa-spin"></i> <?= Yii::t('hipanel', 'loading...') ?>
                            </div>
                            <button type="button" class="btn btn-success btn-box-tool" style="color: #fff;">
                                <?= Yii::t('hipanel:document', 'Start generation') ?>
                            </button>
                        </div>
                    </div>
                    <div class="box-body no-padding">
                        <?= StatisticTableGenerator::widget(compact('type', 'statistic')) ?>
                    </div>
                    <div class="overlay" style="display: none;">
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
