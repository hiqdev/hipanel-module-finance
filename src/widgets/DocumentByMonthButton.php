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

use DateTime;
use yii\base\Widget;

class DocumentByMonthButton extends Widget
{
    public $model;

    public $action;

    public $type;

    public $prepend;

    public $append;

    public $modalHeader;

    public $modalHeaderColor = '';

    public $buttonLabel;

    public function run()
    {
        $dt = new DateTime();
        $this->model->month = $dt->format('Y-m'); // Set default value

        return $this->render('DocumentByMonthButton', [
            'model' => $this->model,
            'action' => $this->action,
            'type' => $this->type,
            'append' => $this->append,
            'prepend' => $this->prepend,
            'modalHeader' => $this->modalHeader,
            'modalHeaderColor' => $this->modalHeaderColor,
            'buttonLabel' => $this->buttonLabel,
            'dt' => $dt,
        ]);
    }
}
