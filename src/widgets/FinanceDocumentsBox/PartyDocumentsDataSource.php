<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets\FinanceDocumentsBox;

use hipanel\modules\client\models\Client;
use hipanel\modules\client\models\Contact;
use Yii;
use yii\helpers\Json;
use yii\web\Application;

final class PartyDocumentsDataSource implements FinanceDocumentsDataSource
{
    use FinanceDocumentsSerializerTrait;

    private \yii\console\Application|null|Application $app;

    /** @param Contact $contact Model with a populated `documents` relation (Contact, Requisite, etc.) */
    public function __construct(readonly private Contact $contact, readonly private Client $client)
    {
        $this->app = Yii::$app;
    }

    public function getMountFunctionName(): string
    {
        return 'mountPartyDocuments';
    }

    public function buildJsProps(): string
    {
        $filtered = $this->filterAccessibleDocuments(
            $this->app,
            $this->contact->documents ?? [],
            $this->client->isEmployee()
        );
        $documents = array_map(fn($d) => $this->serializeDocument($this->app, $d), $filtered);

        return Json::encode(
            [
                'language' => $this->app->language,
                'documents' => $documents,
                'permissions' => $this->buildPermissionList($this->app, $this->client),
            ],
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );
    }

    public function hasDocuments(): bool
    {
        return $this->contact->isRelationPopulated('documents');
    }
}
