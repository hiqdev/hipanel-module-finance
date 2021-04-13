<?php

declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\forms\BillImportFromFileForm;
use hipanel\modules\finance\helpers\parsers\BillsParser;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class BillImportFromFileAction extends BillManagementAction
{
    public function run()
    {
        $form = new BillImportFromFileForm();
        if ($this->controller->request->isPost) {
            $form->file = UploadedFile::getInstance($form, 'file');
            if (!$form->validate()) {
                Yii::$app->getSession()->setFlash('error', $form->getFirstError('file'));

                $this->redirect();
            }
            $bills = $this->parse($form);
            if (empty($bills)) {
                Yii::$app->getSession()->setFlash('error', Yii::t('hipanel:finance', 'Failed to generate any bills from this file'));

                $this->redirect();
            }
            [$billTypes, $billGroupLabels] = $this->billTypesProvider->getGroupedList();
            $billForms = BillForm::createMultipleFromBills($bills, $this->scenario);
            $this->createCollection();
            $this->collection->set($billForms);

            return $this->render('create', [
                'models' => $bills,
                'model' => reset($bills),
                'billTypes' => $billTypes,
                'billGroupLabels' => $billGroupLabels,
            ]);
        }

        throw new BadRequestHttpException('unknown error while importing bills');
    }

    private function parse(BillImportFromFileForm $form): array
    {
        $parser = Yii::$container->get(BillsParser::class);

        return $parser->__invoke($form);
    }

    private function redirect(): Response
    {
        return $this->controller->redirect(['@bill/index']);
    }
}
