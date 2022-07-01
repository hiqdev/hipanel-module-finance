<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\forms\GenerateInvoiceForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\providers\BillTypesProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Session;
use RuntimeException;
use Exception;

class GenerateInvoiceAction extends BillManagementAction
{
    private Session $session;
    private bool $isAjax;
    private bool $isPost;

    public function __construct($id, Controller $controller, BillTypesProvider $billTypesProvider, Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $billTypesProvider, $config);
        $this->session = $session;
        $this->isAjax = $controller->request->isAjax;
        $this->isPost = $controller->request->isPost;
    }

    public function run()
    {
        try {
            $form = new GenerateInvoiceForm();
            $isLoad = $form->load($this->controller->request->post());
            if ($this->isAjax && !$isLoad) {
                $billIds = $this->controller->request->post('selection', []);
                $bills = Bill::find()->select(['*'])->where(['ids' => $billIds])->limit(-1)->all();
                if (empty($billIds)) {
                    throw new BadRequestHttpException('No bills selected');
                }
                $form->requisite_id = $this->getRequisiteId($bills);
                $form->purse_id = $this->getPurseId($bills);

                return $this->controller->renderAjax('modals/generate-invoice', [
                    'model' => $form,
                ]);
            }
            if ($this->isAjax && $isLoad) {
                $d = Purse::perform('generate-document', [
                    'id' => $form->purse_id,
                    'type' => 'invoice',
                    'save' => false,
                ]);
            }
            if ($this->isPost && $isLoad) {
                $form->validate();
                if ($form->hasErrors()) {
                    $errors = $form->getFirstErrors();
                    throw  new RuntimeException(reset($errors));
                }
                throw new BadRequestHttpException('unknown error while creating invoice');
            }
        } catch (Exception $e) {
            $this->session->setFlash('error', $e->getMessage());

            return $this->controller->redirect($this->controller->request->referrer);
        }
    }

    private function getRequisiteId(array $bills): ?int
    {
        $requisiteIds = array_unique(array_filter(ArrayHelper::getColumn($bills, 'requisite_id')));
        if (count($requisiteIds) > 1) {
            return null;
        }

        return reset($requisiteIds);
    }

    private function getPurseId(array $bills): ?int
    {
        $ids = array_unique(array_filter(ArrayHelper::getColumn($bills, 'purse_id')));
        if (count($ids) > 1) {
            throw  new RuntimeException('Purses more than one!');
        }

        return reset($ids);
    }
}
