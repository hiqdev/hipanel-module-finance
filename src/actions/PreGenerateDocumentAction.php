<?php declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\actions\Action;
use hipanel\modules\finance\models\Purse;
use yii\web\Response;

class PreGenerateDocumentAction extends Action
{
    public function run(string $type, string $client_id): ?Response
    {
        $purse = new Purse(['scenario' => 'generate-and-save-monthly-document']);
        $post = $this->controller->request->post();
        if (!$purse->load($post)) {
            $purse->load($post, '');
        }

        $valid = $purse->validate();

        if ($this->controller->request->isAjax) {
            return $this->ajaxResponse($valid, $purse, $type, $client_id);
        }

        return $this->httpResponse($valid, $purse, $type, $client_id);
    }

    private function ajaxResponse(bool $valid, Purse $purse, string $type, string $client_id): Response
    {
        return $this->asJson([
            'status' => $valid ? 'success' : 'error',
            'errors' => $valid ? [] : $purse->getErrors(),
            'message' => $valid ? 'Test data.' : 'Validation failed.',
            'data' => $valid ? [
                [
                    'id' => mt_rand(),
                    'type' => $type,
                    'type_label' => $type,
                    'filename' => mt_rand() . '.pdf',
                    'file_id' => mt_rand(),
                    'number' => mt_rand(),
                    'date' => date('Y-m-d'),
                ],
            ] : [],
        ]);
    }

    private function httpResponse(bool $valid, Purse $purse, string $type, string $client_id): ?Response
    {
        if (!$valid) {
            return null;
        }

        return $this->controller->redirect(array_merge([
            '@purse/generate-monthly-document',
            'type' => $type,
            'client_id' => $client_id,
        ], $purse->toArray()));
    }
}
