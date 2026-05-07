<?php declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use Exception;
use hipanel\actions\Action;
use hipanel\modules\finance\helpers\DocumentGenerationErrorOps;
use hipanel\modules\finance\models\Purse;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;

class PreviewDocumentAction extends Action
{
    public string $action;

    public function __construct($id, Controller $controller, private readonly Session $session, array $config = [])
    {
        parent::__construct($id, $controller, $config);
    }

    public function run(?string $id = null, ?string $type = null): mixed
    {
        $params = ($id !== null && $type !== null)
            ? ['id' => $id, 'type' => $type]
            : $this->controller->request->get();

        $error = null;
        $content = null;
        try {
            $content = Purse::perform($this->action, $params);
        } catch (Exception $e) {
            $error = $e;
        }

        if ($this->controller->request->isAjax) {
            return $this->ajaxResponse($error, $content);
        }

        return $this->httpResponse($error, $content, $params);
    }

    private function ajaxResponse(?Exception $error, mixed $content): mixed
    {
        return $this->asJson([
            'status' => $error !== null ? 'error' : 'success',
            'data' => $content,
            'errors' => $error !== null ? DocumentGenerationErrorOps::extract($error->getResponse()->getData()) : [],
        ]);
    }

    private function httpResponse(?Exception $error, mixed $content, array $params): mixed
    {
        if ($error !== null) {
            $errorOps = DocumentGenerationErrorOps::extract($error->getResponse()->getData());
            $this->session->setFlash('error', DocumentGenerationErrorOps::buildMessage($errorOps));

            return $this->redirectAfterFailure($params);
        }

        $this->asPdf();

        return $content;
    }

    private function redirectAfterFailure(array $params): Response
    {
        if (isset($params['client_id'])) {
            return $this->controller->redirect(['@client/view', 'id' => $params['client_id']]);
        }

        return $this->controller->redirect($this->controller->request->referrer ?: ['index']);
    }

    private function asPdf(): void
    {
        $this->controller->response->format = Response::FORMAT_RAW;
        $this->controller->response->getHeaders()->add('content-type', 'application/pdf');
    }
}
