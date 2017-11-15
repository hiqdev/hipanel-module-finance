<?php

namespace hipanel\modules\finance\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Url;
use yii\web\View;

class StatisticTableGenerator extends Widget
{
    /**
     * @var array
     */
    public $statistic = [];

    /**
     * @var string
     */
    public $type;

    public function init()
    {
        if (!$this->type) {
            throw new InvalidConfigException('Attribute `type` must be set.');
        }
        if (!empty($this->statistic)) {
            $this->sortStatistic();
        }
        $this->initClientScript();
    }

    public function run()
    {
        return $this->render('statisticTableGenerator', ['id' => $this->getId(), 'statistic' => $this->statistic]);
    }

    protected function sortStatistic()
    {
    }

    protected function initClientScript()
    {
        $id = $this->getId();
        $type = $this->type;
        $url = Url::to(['@purse/generate-all']);

        $this->view->registerJs(<<<"JS"
        (function() {
            function updateTable() {
                var table = $('#{$id}');
                var loading = table.parents('.box').find('.loading');
                $.ajax({
                    url: '{$url}',
                    method: 'POST',
                    data: {type: '{$type}'},
                    dataType: 'html',
                    beforeSend: function( xhr ) {
                        loading.show();
                    }
                }).done(function (data) {
                    loading.hide();
                });
            }
            setInterval(updateTable, 10000);
        })();
JS
            , View::POS_END);
    }
}
