<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\actions;

use hipanel\modules\finance\actions\GenerateAndSaveDocumentAction;
use hipanel\modules\finance\tests\unit\support\TestCollection;
use hipanel\modules\finance\tests\unit\support\TestHiartResponse;
use hipanel\modules\finance\tests\unit\support\TestMessageSource;
use hipanel\modules\finance\tests\unit\support\TestRequest;
use hipanel\modules\finance\tests\unit\support\TestSession;
use hipanel\modules\finance\tests\unit\support\TestUser;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\hiart\Collection;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\Controller;
use yii\base\Module;

final class GenerateAndSaveDocumentActionTest extends TestCase
{
    private const ERROR_MESSAGE = 'No templates for requisite.';
    private const REQUISITE_ID = 555;
    private const ITEM_ID = 777;

    private object $originalSession;
    private object $originalUser;
    private array $originalI18nTranslations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalSession = Yii::$app->session;
        $this->originalUser = Yii::$app->user;
        $this->originalI18nTranslations = Yii::$app->i18n->translations;

        Yii::setAlias('@requisite', '/requisite');
        Yii::$app->set('session', new TestSession());
        Yii::$app->set('user', new TestUser(['canMap' => ['requisites.update' => true]]));
        Yii::$app->i18n->translations['hipanel*'] = [
            'class' => TestMessageSource::class,
        ];
    }

    protected function tearDown(): void
    {
        Yii::$app->set('session', $this->originalSession);
        Yii::$app->set('user', $this->originalUser);
        Yii::$app->i18n->translations = $this->originalI18nTranslations;

        parent::tearDown();
    }

    public function testCreatesErrorFlashWithLinkWhenErrorOpsIsNested(): void
    {
        $payload = [
            (string)self::ITEM_ID => [
                'id' => self::ITEM_ID,
                'type' => 'purchase_invoice',
                '_error' => 'failed find document template',
                '_error_ops' => [
                    'requisite_id' => self::REQUISITE_ID,
                    'type' => 'purchase_invoice',
                ],
            ],
            '_error' => self::ERROR_MESSAGE,
        ];

        $action = $this->createActionWithSaveException($payload);

        $error = $action->perform();
        $action->addFlash('error', $error);

        $flash = $this->getSingleFlash('error');
        $this->assertStringContainsString('No templates for requisite. Follow this link', $flash['text']);
        $this->assertStringContainsString((string)self::REQUISITE_ID, $flash['text']);
        $this->assertStringContainsString('purchase_invoice', $flash['text']);
    }

    public function testCreatesErrorFlashWithLinkWhenErrorOpsIsDeeplyNestedInPartialFailure(): void
    {
        $requisiteId = 684291753;

        $payload = [
            '128374650' => [
                '918273645' => [
                    'purse_id' => 128374650,
                    'requisite_id' => $requisiteId,
                    'bill_ids' => ['918273645' => 918273645],
                    'takeOutCharges' => false,
                    'month' => '2026-04-01',
                    'vat_rate' => null,
                    '_error' => 'failed find document template',
                    '_error_ops' => [
                        'requisite_id' => $requisiteId,
                        'type' => 'internal_invoice',
                    ],
                ],
                '564738291' => [
                    'id' => 135792468,
                    'file_id' => 246813579,
                    'sender_id' => 192837465,
                    'receiver_id' => 918264357,
                    'object_id' => 128374650,
                    'type' => 'internal_invoice',
                    'title' => 'internal_invoice template',
                    'client_id' => 918264357,
                    'client' => 'random_client',
                    'status_types' => null,
                    'description' => 'internal_invoice template',
                    'templateid' => null,
                    'validity_start' => '2026-04-01',
                    'validity_end' => '2026-05-01',
                    'data' => null,
                    'type_id' => 357159486,
                ],
                '_error' => 'partially failed (1/2): failed find document template',
            ],
            '_error' => 'partially failed (1/2): failed find document template',
        ];

        $action = $this->createActionWithSaveException($payload);

        $error = $action->perform();
        $action->addFlash('error', $error);

        $flash = $this->getSingleFlash('error');
        $this->assertStringContainsString('No templates for requisite. Follow this link', $flash['text']);
        $this->assertStringContainsString((string)$requisiteId, $flash['text']);
        $this->assertStringContainsString('internal_invoice', $flash['text']);
    }

    public function testCreatesErrorFlashWithLinkForTemplateErrorFromMixedErrorList(): void
    {
        $unrelatedRequisiteId = 473920184;
        $templateRequisiteId = 862904715;

        $payload = [
            '412839576' => [
                '_error' => 'failed validate document payload',
                '_error_ops' => [
                    'requisite_id' => $unrelatedRequisiteId,
                    'type' => 'purchase_invoice',
                ],
            ],
            '675849302' => [
                '_error' => 'failed find document template',
                '_error_ops' => [
                    'requisite_id' => $templateRequisiteId,
                    'type' => 'internal_invoice',
                ],
            ],
            '_error' => 'multiple errors occurred',
        ];

        $action = $this->createActionWithSaveException($payload);

        $error = $action->perform();
        $action->addFlash('error', $error);

        $flash = $this->getSingleFlash('error');
        $this->assertStringContainsString((string)$templateRequisiteId, $flash['text']);
        $this->assertStringContainsString('internal_invoice', $flash['text']);
        $this->assertStringNotContainsString((string)$unrelatedRequisiteId, $flash['text']);
    }

    public function testCreatesErrorFlashWithLinkWhenErrorOpsIsInRoot(): void
    {
        $payload = [
            '_error' => 'failed find document template',
            '_error_ops' => [
                'requisite_id' => self::REQUISITE_ID,
                'type' => 'purchase_invoice',
            ],
        ];

        $action = $this->createActionWithSaveException($payload);

        $error = $action->perform();
        $action->addFlash('error', $error);

        $flash = $this->getSingleFlash('error');
        $this->assertStringContainsString('No templates for requisite. Follow this link', $flash['text']);
        $this->assertStringContainsString((string)self::REQUISITE_ID, $flash['text']);
        $this->assertStringContainsString('purchase_invoice', $flash['text']);
    }

    public function testCreatesErrorFlashWithLinkWhenErrorIsNoTemplatesMessage(): void
    {
        $payload = [
            '_error' => 'No templates for requisite',
            '_error_ops' => [
                'requisite_id' => self::REQUISITE_ID,
                'type' => 'purchase_invoice',
            ],
        ];

        $action = $this->createActionWithSaveException($payload);

        $error = $action->perform();
        $action->addFlash('error', $error);

        $flash = $this->getSingleFlash('error');
        $this->assertStringContainsString('No templates for requisite. Follow this link', $flash['text']);
        $this->assertStringContainsString((string)self::REQUISITE_ID, $flash['text']);
    }

    public function testCreatesContactErrorFlashWhenUserCannotUpdateRequisites(): void
    {
        Yii::$app->set('user', new TestUser(['canMap' => ['requisites.update' => false]]));

        $payload = [
            '_error' => 'failed find document template',
            '_error_ops' => [
                'requisite_id' => self::REQUISITE_ID,
                'type' => 'purchase_invoice',
            ],
        ];

        $action = $this->createActionWithSaveException($payload);

        $error = $action->perform();
        $action->addFlash('error', $error);

        $flash = $this->getSingleFlash('error');
        $this->assertStringContainsString('Please contact finance department', $flash['text']);
        $this->assertStringNotContainsString('Follow this link', $flash['text']);
    }

    public function testCreatesDefaultErrorFlashWhenErrorOpsIsMissing(): void
    {
        $payload = [
            '_error' => self::ERROR_MESSAGE,
        ];

        $action = $this->createActionWithSaveException($payload);

        $error = $action->perform();
        $action->addFlash('error', $error);

        $flash = $this->getSingleFlash('error');
        $this->assertSame(self::ERROR_MESSAGE, $flash['text']);
    }

    public function testCreatesSuccessFlashWhenResponseHasNoError(): void
    {
        $collection = new TestCollection();
        $collection->saveResult = true;

        $action = $this->createAction($collection);

        $error = $action->perform();
        $this->assertFalse($error);

        $action->addFlash('success');

        $flash = $this->getSingleFlash('success');
        $this->assertSame('Document updated', $flash['text']);
    }

    public function testCreatesGenericErrorFlashWhenSaveReturnsFalseWithoutCollectionErrors(): void
    {
        $collection = new TestCollection();
        $collection->saveResult = false;

        $action = $this->createAction($collection);

        $error = $action->perform();
        $this->assertTrue($error);

        $action->addFlash('error', $error);

        $flash = $this->getSingleFlash('error');
        $this->assertIsString($flash['text']);
    }

    private function createActionWithSaveException(array $payload): GenerateAndSaveDocumentAction
    {
        $collection = new TestCollection();
        $collection->saveThrowable = $this->createResponseErrorException($payload);

        return $this->createAction($collection);
    }

    private function createAction(Collection $collection): GenerateAndSaveDocumentAction
    {
        $controller = new Controller('test', new Module('test'));
        $action = new GenerateAndSaveDocumentAction('generate-and-save-monthly-document', $controller, [
            'success' => 'Document updated',
        ]);
        $action->setCollection($collection);

        return $action;
    }

    private function createResponseErrorException(array $payload): ResponseErrorException
    {
        $request = new TestRequest();
        $response = new TestHiartResponse($request, $payload);

        return new ResponseErrorException(self::ERROR_MESSAGE, $response);
    }

    private function getSingleFlash(string $type): array
    {
        $session = Yii::$app->session;
        $this->assertInstanceOf(TestSession::class, $session);
        $this->assertCount(1, $session->flashes[$type] ?? []);

        return $session->flashes[$type][0];
    }
}
