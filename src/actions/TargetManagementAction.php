<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use Exception;
use hipanel\hiart\hiapi\Connection;
use hipanel\hiart\hiapi\HiapiConnectionInterface;
use hipanel\modules\finance\forms\TargetManagementForm;
use hipanel\modules\finance\models\Target;
use RuntimeException;
use Yii;
use yii\base\Action;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Session;

final class TargetManagementAction extends Action
{
    private Session $session;

    private Request $request;

    private Connection $api;

    public function __construct($id, Controller $controller, Session $session, HiapiConnectionInterface $api, array $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->session = $session;
        $this->request = $this->controller->request;
        $this->api = $api;
    }

    public function run()
    {
        $model = new TargetManagementForm(['scenario' => $this->id]);
        if ($this->request->isPost) {
            $model->load($this->request->post());
            try {
                if (!$model->validate()) {
                    $errors = $model->getFirstErrors();
                    throw new RuntimeException(reset($errors));
                }
                $model->submit($this->api);
                $this->session->addFlash('success', $this->getSuccessMessage($model->scenario));
            } catch (Exception $e) {
                $this->session->addFlash('error', $e->getMessage());
            }

            return $this->controller->redirect($this->request->referrer);
        }
        $target = Target::find()->where(['id' => $this->request->get('id')])->withSales()->one();
        $model->fillFromTarget($target);

        return $this->controller->renderAjax("@vendor/hiqdev/hipanel-module-finance/src/views/target/modals/$model->scenario", [
            'model' => $model,
        ]);
    }

    public function getSuccessMessage(string $scenario): string
    {
        $variants = [
            TargetManagementForm::SCENARIO_CHANGE_PLAN => Yii::t('hipanel:finance', 'Target\'s plan has been changed'),
            TargetManagementForm::SCENARIO_CLOSE_SALE => Yii::t('hipanel:finance', 'Target\'s sale has been closed'),
            TargetManagementForm::SCENARIO_SALE => Yii::t('hipanel:finance', 'Target has been sold'),
        ];

        return $variants[$scenario];
    }
}
