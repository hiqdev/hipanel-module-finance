<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;
use yii\helpers\Url;
use yii\web\View;

class ProcessTableGenerator extends Widget
{
    public array $statistic = [];

    public function init(): void
    {
        $this->initClientScript();
    }

    public function run(): string
    {
        return $this->render('processTableGenerator', ['statistic' => $this->statistic['mask'] ?? []]);
    }

    protected function initClientScript(): void
    {
        $id = $this->getId();
        $url = Url::to(['@purse/calculate-costprice']);
        $urlRecalculate = Url::to(['@purse/recalculate']);
        $this->view->registerJs(/* @lang JavaScript */ <<<JS
(() => {
  const loading = $(".costprice-box .loading");
  function updateTable() {
    $.ajax({
      url: "$url",
      method: "POST",
      dataType: "html",
      beforeSend: function( xhr ) {
          loading.css("display","inline-block");
      },
    }).done(function (data) {
      loading.hide();
      $(".costprice-box .box-body").html(data);
    });
  }
  function sendRecalculate(e) {
    e.preventDefault();
    $.ajax({
      url: "$urlRecalculate",
      method: "POST",
      dataType: "json",
      data: { "type": $("#costprice-type").val(), "month": $("#costprice-month").val() },
      beforeSend: function (xhr) {
        loading.css("display", "block");
      },
    }).done(function (data) {
      loading.hide();
    });
  }
  setInterval(updateTable, 2000);
  $(".box-widget form").on("submit", sendRecalculate);
})();
JS
            ,
            View::POS_END);
    }
}
