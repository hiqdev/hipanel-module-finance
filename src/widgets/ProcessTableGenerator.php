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
          const runProgress = () => {
            hipanel.progress("$calculateUrl").onMessage((event) => {
              $(".costprice-box .box-body").html(event.data);
            });
          }
          function sendRecalculate(e) {
            e.preventDefault();
            if (loading.is(":visible")) {
              return;
            }
            
            var value = $("#costprice-type").val();
            value = value.replace('_split', '').toLowerCase();
            if (
                value != 'all'
                && $('.progress-text:contains(' + value.toLowerCase() + ')').siblings('.progress-description').find('.active:contains("progress")').text() != ''
            ) {
                hipanel.notify.error('Recalculation of this type has already been sent');
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
                runProgress();
                hipanel.notify.success(`Recalculation request has been sent`);
              }
            );
          }
          runProgress();
          $(".costprice-box form").on("submit", sendRecalculate);
        })();
JS
            ,
            View::POS_END);
    }
}
