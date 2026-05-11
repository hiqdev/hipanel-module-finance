<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets\FinanceDocumentsBox;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\models\Purse;
use yii\helpers\Json;
use yii\web\Application;

final class PursesDocumentsDataSource implements FinanceDocumentsDataSource
{
    use FinanceDocumentsSerializerTrait;

    /** @param Purse[] $purses */
    public function __construct(readonly private array $purses, readonly private Client $client)
    {
    }

    public function getMountFunctionName(): string
    {
        return 'mountPursesDocuments';
    }

    public function buildJsProps(Application $app): string
    {
        return Json::encode(
            [
                'language' => $app->language,
                'purses' => array_map(fn($p) => $this->serializePurse($app, $p), $this->purses),
                'permissions' => $this->buildPermissionList($app, $this->client),
            ],
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );
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
}
