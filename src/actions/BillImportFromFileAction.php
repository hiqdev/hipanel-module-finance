<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\forms\BillImportFromFileForm;
use hipanel\modules\finance\helpers\parser\BillsImporter;
use hipanel\modules\finance\helpers\parser\NoParserApproiteType;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class BillImportFromFileAction extends BillManagementAction
{
    public function run()
    {
        $form = new BillImportFromFileForm();
        $session = Yii::$app->getSession();
        if ($this->request->isPost) {
            $form->load($this->request->post());
            $form->file = UploadedFile::getInstance($form, 'file');
//            if (!$form->validate()) {
//                $errors = $form->getFirstErrors();
//                $session->setFlash('error', implode("\n", $errors));
//
//                return $this->redirect();
//            }
            try {
                $bills = $this->parse($form);
            } catch (NoParserApproiteType $exception) {
                $session->setFlash('error', Yii::t('hipanel:finance', 'No parser appropriate type'));

                return $this->redirect();
            }
            if (empty($bills)) {
                $session->setFlash('error', Yii::t('hipanel:finance', 'Failed to generate any bills from this file'));

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

        throw new BadRequestHttpException('unknown error while importing bills');
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
