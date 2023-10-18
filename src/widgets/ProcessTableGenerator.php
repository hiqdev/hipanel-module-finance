<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;
use yii\helpers\Url;
use yii\web\View;

class ProcessTableGenerator extends Widget
{
    public array $statistic = [];

    public function run(): string
    {
        $this->initClientScript();

        return $this->render('processTableGenerator', ['statistic' => $this->statistic['mask'] ?? []]);
    }

    protected function initClientScript(): void
    {
        $calculateUrl = Url::to(['@purse/calculate']);
        $recalculateUrl = Url::to(['@purse/recalculate']);
        $this->view->registerJs(/* @lang JavaScript */ <<<JS
        (() => {
          const loading = $(".costprice-box .loading");
          function sendRecalculate(e) {
            e.preventDefault();
            if (loading.is(":visible")) {
              return;
            }
            hipanel.runProcess(
              "$recalculateUrl",
              { type: $("#costprice-type").val(), month: $("#costprice-month").val() },
              () => {
                loading.css("display", "inline-block");
              },
              () => {
                loading.hide();
                hipanel.notify.success(`Recalculation request has been sent`);
              }
            );
          }
          hipanel.progress("$calculateUrl").onMessage((event) => {
            $(".costprice-box .box-body").html(event.data);
          });
          $(".costprice-box form").on("submit", sendRecalculate);
        })();
JS
            ,
            View::POS_END);
    }
}
