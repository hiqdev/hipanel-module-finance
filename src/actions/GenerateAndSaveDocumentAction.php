<?php declare(strict_types=1);

namespace hipanel\modules\finance\actions;

use hipanel\actions\RenderJsonAction;
use hipanel\actions\SmartPerformAction;
use hipanel\modules\finance\helpers\DocumentGenerationErrorOps;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\InvalidCallException;

/**
 *
 * @property-read mixed $defaultRules
 */
final class GenerateAndSaveDocumentAction extends SmartPerformAction
{
    private mixed $responseData = null;

    public function init(): void
    {
        parent::init();
        $this->collectionLoader = static function (GenerateAndSaveDocumentAction $action): void {
            $request = $action->controller->request;
            $data = $request->post();
            if ($request->isAjax && !isset($data[$action->collection->getModel()->formName()])) {
                $action->collection->load([$data]);
            } else {
                $action->collection->load();
            }
        };
    }

    public function getDefaultRules(): array
    {
        return array_merge(parent::getDefaultRules(), [
            'POST ajax' => [
                'save' => true,
                'flash' => true,
                'success' => [
                    'class' => RenderJsonAction::class,
                    'return' => function () {
                        $message = Yii::$app->session->removeFlash('success');

                        return [
                            'status' => 'success',
                            'message' => Yii::t('hipanel:client', reset($message)['text']),
                        ];
                    },
                ],
                'error' => [
                    'class' => RenderJsonAction::class,
                    'return' => function () {
                        $message = Yii::$app->session->removeFlash('error');

                        return [
                            'status' => 'error',
                            'message' => reset($message)['text'],
                            'errors' => $message,
                        ];
                    },
                ],
            ],
        ]);
    }

    /**
     * Overrides SmartPerformAction::perform() to always execute the save regardless of rule->save,
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

    public function addFlash($type, $error = null): void
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

        return DocumentGenerationErrorOps::buildMessage($errorOps);
    }
}
