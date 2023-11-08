<?php

use hipanel\assets\BootstrapDatetimepickerAsset;
use hipanel\helpers\Url;
use hipanel\modules\finance\widgets\PnlAggregateDataTable;

/** @var array $aggregateData */

$this->title = Yii::t('hipanel:finance', 'P&L Calculation');
$this->params['breadcrumbs'][] = $this->title;

BootstrapDatetimepickerAsset::register($this);

$attributes = [
    'month_from' => Yii::t('hipanel:finance', 'Start month'),
    'month_till' => Yii::t('hipanel:finance', 'End month'),
];
$locale = Yii::$app->language;
$mainUrl = Url::to('/finance/pnl/calculation');
$progressUrl = Url::to('/finance/pnl/calculation-progress');

$this->registerCss(".popover { max-width: 600px; }");

$this->registerJs(<<<"JS"
const dateInputs = $(".pnl-box input[id*=month_]");
dateInputs.datetimepicker({
  minDate: moment("2010-06-01"),
  maxDate: moment(),
  locale: "$locale",
  viewMode: "months",
  format: "MMMM YYYY",
});
function startCalculation(event) {
  event.preventDefault();
  const loading = $(".pnl-box .loading");
  const btn = $("button", this).button("loading");
  loading.show();
  const formData = new FormData(event.target);
  const from = moment(formData.get("month_from"));
  const till = moment(formData.get("month_till"));
  const betweenMonths = [];
  if (from < till) {
   const date = from.startOf("month");
   while (date < till.endOf("month")) {
      betweenMonths.push(date.format("YYYY-MM-01"));
      date.add(1, "month");
   }
  } else {
    betweenMonths.push(from.format("YYYY-MM-01"));
  }
  const urls = [];
  betweenMonths.forEach(month => {
    urls.push(`$mainUrl?month=\${month}`);
  });
  Promise.allSettled(urls.map(url => fetch(url)))
  .then(results => {
    results.forEach((result, num) => {
      const date = moment(urls[num].split("=")[1]).format("MMMM YYYY");
      if (result.status == "fulfilled") {
        hipanel.notify.success(`The request for \${date} has been successfully calculated`)
      }
      if (result.status == "rejected") {
        hipanel.notify.error(`The request for \${date} has failed`)
      }
    });

    return results;
  })
  .then(results => Promise.all(results.map(r => r.value.json())))
  .then(results => {
    btn.button("reset");
    loading.hide();
    $(".pnl-box .box-body").load("$mainUrl", function () {
        results.forEach((set) => {
          if (set.uncategorized > 0) {
            const date = set.month;
            const cell = $(`#\${date} .uncategorized`);
            $(".glyphicon", cell).show();
            cell.addClass("clickable");
            cell.click((event) => {
              $(event.target).toggleClass("active");
            });
            cell.popover({
              container: "body",
              content: set.chargesInfo.join("<br>"),
              sanitize: false,
              html: true,
              trigger: "click",
            });
          }
        });
      });
    });

  return false;
}

$(".pnl-box form").on("submit", startCalculation);
JS
);

?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-success pnl-box">
            <div class="box-header with-border">
                <form>
                    <div class="row">
                        <?php foreach ($attributes as $id => $label) : ?>
                            <div class="col-md-3">
                                <div class="form-group has-feedback">
                                    <input type="text" class="form-control" id="<?= $id ?>" name="<?= $id ?>" placeholder="<?= $label ?>">
                                    <span class="glyphicon glyphicon-calendar form-control-feedback text-muted"></span>
                                </div>
                            </div>
                        <?php endforeach ?>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-sm" data-loading-text="Calculation...">
                                <?= Yii::t('hipanel:finance', 'Start calculation') ?>
                            </button>
                            <span class="loading text-muted" style="display: none;">
                                <i class="fa fa-refresh fa-spin"></i> <?= Yii::t('hipanel', 'loading...') ?>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body no-padding">
                <?= PnlAggregateDataTable::widget(['aggregateData' => $aggregateData ?? []]) ?>
            </div>
            <div class="overlay" style="display: none;"></div>
        </div>
    </div>
</div>
