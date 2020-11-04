<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\VdsTariffForm;
use Yii;
use yii\web\NotFoundHttpException;

abstract class VdsTariffManager extends AbstractTariffManager
{
    /**
     * @var VdsTariffForm
     */
    public $form;

    public function init()
    {
        if (!Yii::getAlias('@server', false)) {
            throw new NotFoundHttpException('Server module is missing');
        }

        parent::init();
    }
}
