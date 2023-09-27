<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;
use yii\helpers\Url;
use yii\web\View;

class ProcessTableGenerator extends Widget
{
    /**
     * @var array
     */
    public $statistic = [];

    public function init()
    {
        $this->initClientScript();
    }

    public function run()
    {
        return $this->render('processTableGenerator', ['id' => $this->getId(), 'statistic' => $this->statistic]);
    }

    protected function sortStatistic()
    {
    }

    protected function initClientScript()
    {
        $id = $this->getId();
        $url = Url::to(['@purse/calculate-costprice']);

        $this->view->registerJs(<<<JS
        (function() {
            function updateTable() {
                var table = $('#{$id}');
                var loading = table.parents('.box').find('.loading');
                $.ajax({
                    url: '{$url}',
                    method: 'POST',
                    dataType: 'html',
                    beforeSend: function( xhr ) {
                        loading.css('display','inline-block');
                    }
                }).done(function (data) {
                    loading.hide();
                    $('#{$id} .costprice-table').html(data);
                });
            }
            setInterval(updateTable, 2000);
        })();
JS
            , View::POS_END);

        $urlRecalculate = Url::to(['@purse/recalculate']);

        $this->view->registerJs(<<<JS
            function sendRecalculate() {
                var table = $('#{$id}');
                var loading = table.parents('.box').find('.loading');
                $.ajax({
                    url: '{$urlRecalculate}',
                    method: 'POST',
                    dataType: "json",
                    data:{'type': $('#costprice-type').val(),'month':$('#costprice-month').val()},
                    beforeSend: function( xhr ) {
                        loading.css('display','inline-block');
                    }
                }).done(function (data) {
                    loading.hide();
                })
            };
JS
            , View::POS_END);
    }
}
