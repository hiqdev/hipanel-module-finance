<?php
declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\actions\SmartPerformAction;
use hipanel\helpers\Url;
use hipanel\modules\finance\helpers\DocumentGenerationErrorOps;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\InvalidCallException;
use yii\helpers\Html;

final class GenerateAndSaveMonthlyDocumentAction extends SmartPerformAction
{
    private ?array $responseData = null;

    /**
     * Overrides SwitchAction::perform() to always execute the save regardless of rule->save,
     * and captures response data from ResponseErrorException for use in addFlash().
     */
    public function perform()
    {
        $this->beforePerform();
        $this->loadCollection();

        try {
            $error = !$this->saveCollection();

            if ($error === true && $this->collection->hasErrors()) {
                $error = $this->collection->getFirstError();
            }
        } catch (ResponseErrorException $e) {
            $this->responseData = $e->getResponse()->getData();
            $error = $e->getMessage();
        } catch (InvalidCallException $e) {
            $error = $e->getMessage();
        }

        $this->afterPerform();

        return $error;
    }

    public function addFlash($type, $error = null)
    {
        if ($type === 'error') {
            Yii::$app->session->addFlash('error', ['text' => $this->buildErrorText($error)]);
            return;
        }

        parent::addFlash($type, $error);
    }

    private function buildErrorText(mixed $error): string
    {
        $errorOps = DocumentGenerationErrorOps::extract($this->responseData);

        if ($errorOps === null) {
            return is_string($error) && $error !== ''
                ? Yii::t('hipanel', $error)
                : $this->getFlashText('error');
        }

        if (Yii::$app->user->can('requisites.update')) {
            $requisiteId = $errorOps['requisite_id'];
            $contactUrl = Html::a(
                Url::toRoute(['@requisite/view', 'id' => $requisiteId], true),
                ['@requisite/view', 'id' => $requisiteId]
            );
            return Yii::t(
                'hipanel:finance',
                "No templates for requisite. Follow this link {contactUrl} and set template of type '{type}'",
                ['contactUrl' => $contactUrl, 'type' => $errorOps['type']]
            );
        }

        return Yii::t('hipanel:finance', 'No templates for requisite. Please contact finance department');
    }
}
