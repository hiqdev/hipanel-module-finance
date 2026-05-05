<?php declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\modules\finance\models\Purse;
use yii\base\Action;
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

    private function ajaxResponse(bool $valid, Purse $purse, string $type, string $client_id): ?Response
    {
        // TODO: determine AJAX response behavior
        return null;
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
