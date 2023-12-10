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

use hipanel\modules\finance\models\Plan;
use hipanel\widgets\AjaxModal;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\web\View;

class PnlSetNoteButton extends Widget
{
    /**
     * @var Plan
     */
    public $model;

    public function init()
    {
        $this->view->on(View::EVENT_END_BODY, function () {
            echo AjaxModal::widget([
                'id' => 'pnl-set-note-modal',
                'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Set note'), ['class' => 'modal-title']),
                'scenario' => 'set-note',
                'actionUrl' => ['/finance/pnl/set-note-form'],
                'bulkPage' => true,
                'usePost' => true,
                'size' => AjaxModal::SIZE_LARGE,
                'toggleButton' => false,
            ]);
        });
    }

    public function run()
    {
        return Html::button(Yii::t('hipanel:finance', 'Set note'), [
            'class' => 'btn',
            'data' => [
                'toggle' => 'modal',
                'target' => '#pnl-set-note-modal',
            ],
        ]);
    }
}
