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
  const from = moment().month(formData.get("month_from"));
  const till = moment().month(formData.get("month_till"));
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
    const fulfilled = results.filter(result => result.status === 'fulfilled');
    const rejected = results.filter(result => result.status === 'rejected').map(result => result.reason);
    console.error(rejected);

    return fulfilled;
  })
  .then(results => {
    return Promise.all(results.map(r => {
      const date = moment(r.value.url.split("=")[1]).format("MMMM YYYY");
      if (r.value.ok) {
        hipanel.notify.success(`\${date} has been calculated`);

        return r.value.json();
      } else {
        hipanel.notify.error(`\${date} has been failed, error: ` + r.value.statusText);

        return r.value.text();
      }
    }));
  })
  .then(results => {
    btn.button("reset");
    loading.hide();
    $(".pnl-box .box-body").load("$mainUrl", function () {
      results.forEach((set) => {
        if (typeof set === "string") {
          console.error(set);
        } else if (set.uncategorized > 0) {
          const date = set.month;
          const cell = $(`.\${date} .uncategorized`);
          $(".glyphicon", cell).show();
          $(".glyphicon", cell).addClass("clickable");
          const linkToCharges = $(`.\${date} caption a`).get(0);
          const tbody = $(`.\${date} tbody`).get(0);
          const template = document.querySelector("#charge-info-row");
          set.chargesInfo.forEach(charge => {
            const clone = template.content.cloneNode(true);
            let td = clone.querySelectorAll("td");
            td[0].firstChild.href = `/finance/charge/view?id=\${charge.id}`;
            td[0].firstChild.textContent = charge.id;
            td[1].textContent = charge.label;
            td[2].textContent = charge.type;
            td[3].textContent = charge.object;

            tbody.appendChild(clone);
          });
          linkToCharges.href = "/finance/charge/index?ChargeSearch[ids]=" + set.chargesInfo.map(c => c.id).join(",");
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
                                    <input type="text" class="form-control" id="<?= $id ?>" name="<?= $id ?>" placeholder="<?= $label ?>" required>
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
