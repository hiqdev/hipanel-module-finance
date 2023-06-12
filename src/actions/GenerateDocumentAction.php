<?php
declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use Exception;
use hipanel\modules\document\models\Document;
use hipanel\modules\finance\forms\GenerateInvoiceForm;
use yii\base\Action;
use yii\web\Controller;
use yii\web\Session;

class GenerateDocumentAction extends Action
{
    public function __construct($id, Controller $controller, private readonly Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $config);
    }

    public function run()
    {
        $generateInvoiceForm = new GenerateInvoiceForm();
        try {
            if ($generateInvoiceForm->load($this->controller->request->post())) {
                $response = Document::perform('generate', $generateInvoiceForm->attributes);

                return $this->controller->response->sendContentAsFile(
                    $response,
                    $generateInvoiceForm->filename,
                    ['inline' => true, 'mimeType' => 'application/pdf']
                );
            }
        } catch (Exception $e) {
            $this->session->setFlash('error', $e->getMessage());

            return $this->controller->redirect($this->controller->request->referrer);
        }
    }
}
