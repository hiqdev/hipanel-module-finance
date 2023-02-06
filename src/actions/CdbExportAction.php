<?php
declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use DateTimeImmutable;
use Exception;
use hipanel\modules\finance\forms\CdbExportForm;
use RuntimeException;
use yii\base\Action;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use yii\web\UploadedFile;

class CdbExportAction extends Action
{

    public function __construct($id, Controller $controller, private readonly Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $config);
    }

    public function run(): Response
    {
        $form = new CdbExportForm();
        $form->file = UploadedFile::getInstance($form, 'file');
        try {
            if ($form->validate()) {
                $content = $form->convert();
                if (!$content) {
                    throw new RuntimeException('An error occurred while converting the file');
                }

                return $this->controller->response->sendContentAsFile(
                    $content,
                    implode('.', [(new DateTimeImmutable())->format('YmdHi'), 'xml']),
                    ['mimeType' => 'application/xhtml+xml']
                );
            }
            $this->session->setFlash('error', json_encode($form->getErrors(), JSON_THROW_ON_ERROR));
        } catch (Exception $e) {
            $this->session->setFlash('error', $e->getMessage());
        }

        return $this->controller->redirect($this->controller->request->referrer);
    }
}
