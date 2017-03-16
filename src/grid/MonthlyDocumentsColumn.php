<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\widgets\ArraySpoiler;
use hipanel\helpers\FontIcon;
use hipanel\widgets\ModalButton;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

class MonthlyDocumentsColumn extends DocumentsColumn
{
    protected function getSeeNewRoute($model)
    {
        return ['@purse/generate-monthly-document', 'id' => $model->id, 'type' => $this->type];
    }

    protected function getUpdateButtonRoute($model)
    {
        return ['@purse/generate-and-save-monthly-document'];
    }
}
