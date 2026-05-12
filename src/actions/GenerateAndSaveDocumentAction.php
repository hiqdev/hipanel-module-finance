<?php declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\actions\Action;
use hipanel\modules\finance\helpers\DocumentGenerationErrorOps;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\responses\DocumentGenerationAjaxResponse;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\InvalidCallException;

final class GenerateAndSaveDocumentAction extends Action
{
    public function run()
    {
        $payload = $this->controller->request->post();

        try {
            $rsp = Purse::perform('generate-and-save-monthly-document', $payload);
        } catch (ResponseErrorException $e) {
            $message = DocumentGenerationErrorOps::buildMessage($e->getResponse()->getData());

            return $this->asJson(DocumentGenerationAjaxResponse::error($message)->asArray());
        } catch (InvalidCallException $e) {
            $message = DocumentGenerationErrorOps::buildMessage($e->getMessage());

            return $this->asJson(DocumentGenerationAjaxResponse::error($message)->asArray());
        }

        return $this->asJson(DocumentGenerationAjaxResponse::success((array)$rsp)->asArray());
    }
}
