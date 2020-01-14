<?php
/**
 * Finance plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2020, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;

class TemplatesWidget extends Widget
{
    public $model;

    public $requisiteId;

    /**
     * @var array options for [[Pjax]] widget
     */
    public $pjaxOptions = [];

    /**
     * @var array options for [[Progress]] widget
     */
    public $progressOptions = [];

    /**
     * @var array|string url to send the form
     */
    public $actionUrl = '@requisite/set-templates';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('templates', [
            'model' => $this->model,
            'actionUrl' => $this->actionUrl,
        ]);
    }
}
