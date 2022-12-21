<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\helpers\ArrayHelper;
use hipanel\helpers\Url;
use hipanel\modules\document\models\Document;
use hipanel\modules\finance\forms\GenerateInvoiceForm;
use hipanel\modules\finance\forms\PrepareInvoiceForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\providers\BillTypesProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use RuntimeException;
use Exception;

class GenerateInvoiceAction extends BillManagementAction
{
    private Session $session;

    public function __construct($id, Controller $controller, BillTypesProvider $billTypesProvider, Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $billTypesProvider, $config);
        $this->session = $session;
    }

    public function run()
    {
        try {
            $prepareInvoiceForm = new PrepareInvoiceForm();
            $generateInvoiceForm = new GenerateInvoiceForm();
            if ($this->controller->request->isAjax && $prepareInvoiceForm->load($this->controller->request->post())) {
                $this->controller->response->format = Response::FORMAT_JSON;

                return Document::perform('prepare-invoice', $prepareInvoiceForm->attributes);
            }
            if ($generateInvoiceForm->load($this->controller->request->post())) {
                if ($generateInvoiceForm->save === true) {
                    $response = Document::perform('generate', $generateInvoiceForm->attributes);
                    $this->controller->response->format = Response::FORMAT_JSON;
                    $response['link_to_document'] = Url::to(['@document/view', 'id' => $response['id']]);

                    return $response;
                }
                $generateInvoiceForm->data = json_decode($generateInvoiceForm->data, true, 512, JSON_THROW_ON_ERROR);
                $response = Document::perform('generate', $generateInvoiceForm->attributes);

                return $this->controller->response->sendContentAsFile(
                    $response,
                    $generateInvoiceForm->filename,
                    ['inline' => true, 'mimeType' => 'application/pdf']
                );
            }
            $billIds = $this->controller->request->post('selection', []);
            $bills = $this->getBills($billIds);
            if (empty($billIds)) {
                throw new BadRequestHttpException('No bills selected');
            }
            $prepareInvoiceForm->requisite_id = $this->getRequisiteId($bills);
            $prepareInvoiceForm->purse_id = $this->getPurseId($bills);
            $prepareInvoiceForm->bill_ids = implode(',', $billIds);

            return $this->controller->render('generate-invoice', [
                'model' => $prepareInvoiceForm,
            ]);
        } catch (Exception $e) {
            $this->session->setFlash('error', $e->getMessage());

            return $this->controller->redirect($this->controller->request->referrer);
        }
    }

    private function getRequisiteId(array $bills): ?int
    {
        $requisiteIds = array_unique(array_filter(ArrayHelper::getColumn($bills, 'requisite_id')));
        if (empty($requisiteIds) || count($requisiteIds) > 1) {
            return null;
        }

        return reset($requisiteIds);
    }

    private function getPurseId(array $bills): ?int
    {
        $purseColumn = array_filter(ArrayHelper::getColumn($bills, 'purse_id'));
        if (empty($purseColumn)) {
            return null;
        }
        $ids = array_unique($purseColumn);
        if (count($ids) > 1) {
            throw  new RuntimeException('Purses more than one!');
        }

        return reset($ids);
    }

    private function getBills(array $ids): array
    {
        return Bill::find()->select(['*'])->where(['ids' => $ids])->limit(-1)->all();
    }
}
