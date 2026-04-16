<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\actions;

use hipanel\modules\finance\actions\GenerateAndSaveMonthlyDocumentAction;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\hiart\Collection;
use hiqdev\hiart\RequestInterface;
use hiqdev\hiart\ResponseErrorException;
use hiqdev\hiart\ResponseInterface;
use Yii;
use yii\base\Component;
use yii\base\Controller;
use yii\base\Module;
use yii\i18n\MessageSource;

final class GenerateAndSaveMonthlyDocumentActionTest extends TestCase
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
                '_error' => self::ERROR_MESSAGE,
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

    public function testCreatesErrorFlashWithLinkWhenErrorOpsIsInRoot(): void
    {
        $payload = [
            '_error' => self::ERROR_MESSAGE,
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

    public function testCreatesContactErrorFlashWhenUserCannotUpdateRequisites(): void
    {
        Yii::$app->set('user', new TestUser(['canMap' => ['requisites.update' => false]]));

        $payload = [
            '_error' => self::ERROR_MESSAGE,
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

    private function createActionWithSaveException(array $payload): GenerateAndSaveMonthlyDocumentAction
    {
        $collection = new TestCollection();
        $collection->saveThrowable = $this->createResponseErrorException($payload);

        return $this->createAction($collection);
    }

    private function createAction(Collection $collection): GenerateAndSaveMonthlyDocumentAction
    {
        $controller = new Controller('test', new Module('test'));
        $action = new GenerateAndSaveMonthlyDocumentAction('generate-and-save-monthly-document', $controller, [
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

final class TestSession extends Component
{
    public array $flashes = [];

    public function addFlash($type, $message, $removeAfterAccess = true): void
    {
        $this->flashes[$type][] = $message;
    }
}

final class TestCollection extends Collection
{
    public bool $saveResult = false;
    public ?\Throwable $saveThrowable = null;
    public int $loadCalls = 0;
    public int $saveCalls = 0;

    public function load($data = null)
    {
        ++$this->loadCalls;

        return $this;
    }

    public function save($runValidation = true, $attributes = null, $options = [])
    {
        ++$this->saveCalls;

        if ($this->saveThrowable !== null) {
            throw $this->saveThrowable;
        }

        return $this->saveResult;
    }
}

final class TestRequest implements RequestInterface
{
    public function getDbname()
    {
        return null;
    }

    public function getMethod()
    {
        return 'POST';
    }

    public function getUri()
    {
        return null;
    }

    public function getFullUri()
    {
        return 'http://localhost/purse/generate-and-save-monthly-document';
    }

    public function getHeaders()
    {
        return [];
    }

    public function getBody()
    {
        return '{}';
    }

    public function getVersion()
    {
        return '1.1';
    }

    public function getQuery()
    {
        return null;
    }

    public function build()
    {
    }

    public function send($options = [])
    {
        return null;
    }

    public static function isSupported()
    {
        return true;
    }

    public function serialize(): string
    {
        return '';
    }

    public function unserialize(string $serialized): void
    {
    }

    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {
    }
}

final class TestHiartResponse implements ResponseInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly array $payload,
    ) {
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getData()
    {
        return $this->payload;
    }

    public function getRawData()
    {
        return json_encode($this->payload, JSON_THROW_ON_ERROR);
    }

    public function getStatusCode()
    {
        return 200;
    }

    public function getReasonPhrase()
    {
        return 'OK';
    }

    public function getHeader($name)
    {
        return null;
    }

    public function getHeaders()
    {
        return [];
    }
}

final class TestUser extends Component
{
    public array $canMap = [];

    public function can($permissionName, $params = [], $allowCaching = true): bool
    {
        return $this->canMap[$permissionName] ?? false;
    }
}

final class TestMessageSource extends MessageSource
{
    protected function translateMessage($category, $message, $language): string
    {
        return $message;
    }
}
