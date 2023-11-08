<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\widgets\StatisticTableGenerator;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var array $statisticByTypes */

$this->title = Yii::t('hipanel:finance', 'Generate documents');
$this->params['breadcrumbs'][] = $this->title;

$performGenerationUrl = Url::toRoute('@purse/generation-perform');
$showProgressUrl = Url::to(['@purse/generation-progress']);
$this->registerJsVar("statTypes", array_keys($statisticByTypes));
$this->registerJs(<<<JS
(() => {
  const showProgress = type => {
    hipanel.progress(`$showProgressUrl?type=\${type}`).onMessage((event) => {
      $(`.box-statistic-table.\${type}`).html(event.data);
    });
  }
  const performGeneration = function (e) {
    e.preventDefault();
    const loading = $(e.target).find(".loading");
    const fd = new FormData(e.target);
    const type = fd.get("type");
    if (loading.is(":visible")) {
      return;
    }
    hipanel.runProcess(
      "$performGenerationUrl",
      { type: type },
      () => {
        loading.css("display", "inline-block");
      },
      () => {
        loading.hide();
        showProgress(type);
        hipanel.notify.success(`Generation request has been sent`);
      }
    );
  };
  $(".box form").on("submit", performGeneration);
  statTypes.forEach(type => {
    showProgress(type);
  });
})();
JS
);

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
                        <?php $form = ActiveForm::begin() ?>
                        <h3 class="box-title"><?= Yii::t('hipanel:document', ucfirst($type)) ?></h3>
                        <div class="box-tools pull-right">
                            <div class="loading btn-box-tool" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i> <?= Yii::t('hipanel', 'loading...') ?>
                            </div>
                            <?= Html::hiddenInput('type', $type) ?>
                            <button type="submit" class="btn btn-success btn-box-tool" style="color: #fff;">
                                <?= Yii::t('hipanel:document', 'Start generation') ?>
                            </button>
                        </div>
                        <?php ActiveForm::end() ?>
                    </div>
                    <div class="box-body no-padding box-statistic-table <?= $type ?>" style="margin: 0; padding: 0; border: none;">
                        <?= StatisticTableGenerator::widget(compact('type', 'statistic')) ?>
                    </div>
                    <div class="overlay" style="display: none;">
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php endif ?>
</div>
