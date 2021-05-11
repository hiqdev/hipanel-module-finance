<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use Exception;
use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\forms\BillImportFromFileForm;
use hipanel\modules\finance\helpers\parser\BillsImporter;
use hipanel\modules\finance\helpers\parser\NoParserAppropriateType;
use hipanel\modules\finance\models\Requisite;
use hipanel\modules\finance\providers\BillTypesProvider;
use http\Exception\RuntimeException;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use yii\web\UploadedFile;

class BillImportFromFileAction extends BillManagementAction
{
    private Session $session;

    public function __construct($id, Controller $controller, BillTypesProvider $billTypesProvider, Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $billTypesProvider, $config);
        $this->session = $session;
    }

    public function run()
    {
        $form = new BillImportFromFileForm();
        try {
            if (!$this->request->isPost || !$form->load($this->request->post())) {
                throw new BadRequestHttpException('No form data found');
            }
            $form->file = UploadedFile::getInstance($form, 'file');
            $requisite = Requisite::find()->where(['id' => $form->requisite_id])->one();
            $form->guessTypeByRequisiteName($requisite->name);
            if (!$form->validate()) {
                $errors = $form->getFirstErrors();
                throw new Exception(implode("\n", $errors));
            }
            $bills = $this->parse($form);
            if (empty($bills)) {
                throw new RuntimeException(Yii::t('hipanel:finance', 'No bills to add found'));
            }
        } catch (Exception $exception) {
            $this->session->setFlash('warning', $exception->getMessage());

            return $this->redirect();
        }
        [$billTypes, $billGroupLabels] = $this->billTypesProvider->getGroupedList();
        $billForms = BillForm::createMultipleFromBills($bills, $this->scenario);
        $this->createCollection();
        $this->collection->set($billForms);

        return $this->controller->render('create', [
            'models' => $billForms,
            'model' => reset($billForms),
            'billTypes' => $billTypes,
            'billGroupLabels' => $billGroupLabels,
        ]);
    }

    private function parse(BillImportFromFileForm $form): array
    {
        $parser = Yii::$container->get(BillsImporter::class, [$form]);

        return $parser->__invoke();
    }

    private function redirect(): Response
    {
        return $this->controller->goBack();
    }
}
