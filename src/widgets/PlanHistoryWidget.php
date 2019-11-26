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

use hipanel\helpers\ArrayHelper;
use yii\base\Widget;

class PlanHistoryWidget extends Widget
{
    public $model;

    public function run()
    {
        $planHistory = ArrayHelper::index($this->model->planHistory, 'id', [function ($el) {
            return $el->time;
        }]);

        return $this->render('PlanHistoryWidget', [
            'models' => array_filter($planHistory),
            'widget' => $this,
        ]);
    }
}
