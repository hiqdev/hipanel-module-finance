<?php
declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Url;
use yii\web\View;

class StatisticTableGenerator extends Widget
{
    public array $statistic = [];
    public ?string $type = null;

    public function init()
    {
        if (!$this->type) {
            throw new InvalidConfigException('Attribute `type` must be set.');
        }
    }

    public function run()
    {
        $this->initClientScript();

        return $this->render('statisticTableGenerator', ['type' => $this->type, 'statistic' => $this->statistic]);
    }

    protected function initClientScript()
    {
        $url = Url::to(['@purse/generation-progress', 'type' => $this->type]);

        $this->view->registerJs(<<<"JS"
//          hipanel.progress("$url").onMessage((event) => {
//            $(".box-statistic-table.{$this->type}").html(event.data);
//          });
JS
            ,
            View::POS_END);
    }
}
