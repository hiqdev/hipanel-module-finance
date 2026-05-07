<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\modules\finance\models\InstallmentPlan;
use Yii;
use yii\base\Action;

class InstallmentPlanProcessAction extends Action
{
    public function run()
    {
        if (Yii::$app->request->isPost) {
            try {
                InstallmentPlan::perform('process', [], ['batch' => true]);
                Yii::$app->session->setFlash('success', Yii::t('hipanel:finance', 'Installment plans have been processed'));
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->controller->redirect(['index']);
    }
}
