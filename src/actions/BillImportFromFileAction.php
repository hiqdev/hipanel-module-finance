<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\forms\BillImportFromFileForm;
use hipanel\modules\finance\helpers\parser\BillsImporter;
use hipanel\modules\finance\helpers\parser\NoParserApproiteType;
use hipanel\modules\finance\providers\BillTypesProvider;
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
        if (!$this->request->isPost || !$form->load($this->request->post())) {
            throw new BadRequestHttpException('unknown error while importing bills');
        }
        $form->file = UploadedFile::getInstance($form, 'file');
        if (!$form->validate()) {
            $errors = $form->getFirstErrors();
            $this->session->setFlash('error', implode("\n", $errors));

            return $this->redirect();
        }
        try {
            $bills = $this->parse($form);
        } catch (NoParserApproiteType $exception) {
            $this->session->setFlash('error', Yii::t('hipanel:finance', 'No parser appropriate type'));

            return $this->redirect();
        }
        if (empty($bills)) {
            $this->session->setFlash('warning', Yii::t('hipanel:finance', 'No bills to add found'));

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
