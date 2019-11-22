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

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Widget;

class PlanHistoryWidget extends Widget
{
    public $model;

    public $models;

    public function run()
    {
        if (empty($this->model)) {
            $models = [$this->model];
        } elseif (empty($this->model)) {
            $models = $this->models;
        } else {
            throw new InvalidArgumentException('Model or models must be set');
        }

        return $this->render('PlanHistoryWidget', [
            'models' => $models,
            'widget' => $this,
        ]);
    }
}
