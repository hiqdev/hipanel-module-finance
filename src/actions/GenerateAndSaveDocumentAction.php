<?php declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\actions\Action;
use hipanel\modules\finance\helpers\DocumentGenerationErrorOps;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\responses\DocumentGenerationAjaxResponse;
use hipanel\modules\finance\widgets\FinanceDocumentsBox\FinanceDocumentsSerializerTrait;
use hiqdev\hiart\ResponseErrorException;
use yii\base\InvalidCallException;

final class GenerateAndSaveDocumentAction extends Action
{
    public string $action = 'generate-and-save-document';

    public function run()
    {
        $payload = $this->controller->request->post();

        try {
            $rsp = Purse::batchPerform($this->action, $payload);
        } catch (ResponseErrorException $e) {
            $message = DocumentGenerationErrorOps::buildMessage($e->getResponse()->getData());

            return $this->asJson(DocumentGenerationAjaxResponse::error($message)->asArray());
        } catch (InvalidCallException $e) {
            $message = DocumentGenerationErrorOps::buildMessage($e->getMessage());

            return $this->asJson(DocumentGenerationAjaxResponse::error($message)->asArray());
        }

        $data = array_map([FinanceDocumentsSerializerTrait::class, 'serializeRawDocumentEntry'], (array)$rsp);

        return $this->asJson(DocumentGenerationAjaxResponse::success($data)->asArray());
    }
}
