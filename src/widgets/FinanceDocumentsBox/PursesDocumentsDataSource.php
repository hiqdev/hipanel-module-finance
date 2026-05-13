<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets\FinanceDocumentsBox;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\models\Purse;
use Yii;
use yii\helpers\Json;
use yii\web\Application;

final class PursesDocumentsDataSource implements FinanceDocumentsDataSource
{
    use FinanceDocumentsSerializerTrait;

    private \yii\console\Application|null|Application $app;

    /** @param Purse[] $purses */
    public function __construct(
        readonly private array $purses,
        readonly private Client $client,
        readonly private array $currencies,
        readonly private array $documentTypes,
    )
    {
        $this->app = Yii::$app;
    }

    public function getMountFunctionName(): string
    {
        return 'mountPursesDocuments';
    }

    public function buildJsProps(): string
    {
        $availableTypes = $this->resolveAccessibleDocumentTypes($this->app, $this->client->isEmployee());
        $types = array_filter($this->documentTypes, static fn($type) => in_array($type, $availableTypes, true), ARRAY_FILTER_USE_KEY);

        $payload = [
            'language' => $this->app->language,
            'purses' => array_map(fn($p) => $this->serializePurse($this->app, $p), $this->purses),
            'permissions' => $this->buildPermissionList($this->app, $this->client),
            'currencies' => $this->prepareAssoc($this->currencies),
            'documentTypes' => $this->prepareAssoc($types),
        ];

        return Json::encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    }

    private function serializePurse(Application $app, Purse $purseModel): array
    {
        $purse = $purseModel->toArray();

        $purse['contact'] = $purseModel->contact->toArray();
        $purse['requisite'] = $purseModel->requisite->toArray();

        foreach (['contact', 'requisite'] as $attribute) {
            $purse[$attribute]['bankDetails'] = $purseModel->{$attribute}->hasBankDetails() ? array_map(
                static fn($p) => $p->toArray(),
                $purseModel->{$attribute}->bankDetails
            ) : [];
        }

        $purse['documents'] = [];
        if ($purseModel->isRelationPopulated('documents')) {
            $filtered = $this->filterAccessibleDocuments($app, $purseModel->documents, $purseModel->clientModel->isEmployee());
            $purse['documents'] = array_map(fn($d) => $this->serializeDocument($app, $d), $filtered);
        }

        return $purse;
    }

    private function prepareAssoc(array $assoc): array
    {
        return array_map(
            static fn(string $id, string $label): array => ['id' => $id, 'label' => $label],
            array_keys($assoc),
            $assoc
        );
    }

    /** Checks whether at least one purse has the 'documents' relation populated. */
    public function hasDocuments(): bool
    {
        return array_any($this->purses, fn($purse) => $purse->isRelationPopulated('documents'));
    }
}
