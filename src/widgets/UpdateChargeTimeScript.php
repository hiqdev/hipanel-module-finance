<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\forms\BillForm;
use yii\base\Widget;
use yii\web\JsExpression;

class UpdateChargeTimeScript extends Widget
{
    public BillForm $model;

    public function run()
    {
        $onNewRecordBehavior = new JsExpression(<<<JS
          $(".bills_dynamicform_wrapper").on("afterInsert", (e, el) => {
            $(el).find(".charges_dynamicform_wrapper").on("afterInsert", (e) => updateChargeTime(e));
          });
JS
        );
        $js = $this->model->isNewRecord ? $onNewRecordBehavior : '';
        $this->view->registerJs(<<<"JS"
          ;(() => {
            // auto-update charges time
            const updateChargeTime = (e, item) => {
              const billItem = $(e.target).parents('.bill-item');
              const billTimeInputValue = $(billItem).find(".bill-time").val();
              if (!moment(billTimeInputValue).isValid()) {
                return;
              }
              const billTime = moment(billTimeInputValue);
              if (e.type === "change" && $(billItem).find(".charge-item").length) {
                const needupdateChargesTime = confirm("Do you want to change the time in charges relative to the time of this bill?");
                if (needupdateChargesTime) {
                  let chargeTime = null;
                  $(billItem).find(".charge-item :input[id$=time]").each((idx, chargeTimeInput) => {
                    chargeTime = (chargeTime ?? billTime).add(1, "seconds");
                    $(chargeTimeInput).val(chargeTime.format("YYYY-MM-DD HH:mm:ss"));
                  });

                  return;
                }
              } else {
                // const prevChargeTimeValue = $(billItem).find(".charge-item:last-child").prev().find(':input[id$=time]').val();
                const chargeTimeInput = $(billItem).find(".charge-item:last-child :input[id$=time]");
                // const chargeTime = prevChargeTimeValue ? moment(prevChargeTimeValue).add(1, "seconds") : billTime.add(1, "seconds");
                $(chargeTimeInput).val(moment().format("YYYY-MM-DD HH:mm:ss"));
              }
            };
            $(document).on("change", ".bill-time", updateChargeTime);
            $(".charges_dynamicform_wrapper").on("afterInsert", (e) => updateChargeTime(e));
            $js
          })();
JS
        );
    }
}
