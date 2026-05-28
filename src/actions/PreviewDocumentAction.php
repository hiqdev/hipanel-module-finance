<?php declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use Exception;
use hipanel\actions\Action;
use hipanel\modules\finance\helpers\BatchPerformHelper;
use hipanel\modules\finance\helpers\DocumentGenerationErrorOps;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\responses\DocumentGenerationAjaxResponse;
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

    public function run(): mixed
    {
        $params = $this->controller->request->post();

        $error = null;
        $content = null;
        try {
            $content = Purse::batchPerform($this->action, [$params]);
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
        if ($error !== null) {
            $message = $this->buildErrorMessage($error);

            return $this->asJson(DocumentGenerationAjaxResponse::error($message)->asArray());
        }

        return $this->asJson(DocumentGenerationAjaxResponse::success(BatchPerformHelper::unwrapResults($content))->asArray());
    }

    private function httpResponse(?Exception $error, mixed $content, array $params): mixed
    {
        if ($error !== null) {
            $this->session->setFlash('error', DocumentGenerationErrorOps::buildMessage($error->getResponse()->getData()));

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

    private function buildErrorMessage(Exception $error): string
    {
        if (method_exists($error, 'getResponse')) {
            return DocumentGenerationErrorOps::buildMessage($error->getResponse()->getData());
        }

        return $error->getMessage();
    }
}
