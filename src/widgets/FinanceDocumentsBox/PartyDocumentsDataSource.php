<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets\FinanceDocumentsBox;

use hipanel\modules\client\models\Client;
use hipanel\modules\client\models\Contact;
use yii\helpers\Json;
use yii\web\Application;

final class PartyDocumentsDataSource implements FinanceDocumentsDataSource
{
    use FinanceDocumentsSerializerTrait;

    /** @param Contact $contact Model with a populated `documents` relation (Contact, Requisite, etc.) */
    public function __construct(readonly private Contact $contact, readonly private Client $client)
    {
    }

    public function getMountFunctionName(): string
    {
        return 'mountPartyDocuments';
    }

    public function buildJsProps(Application $app): string
    {
        $filtered = $this->filterAccessibleDocuments(
            $app,
            $this->contact->documents ?? [],
            $this->client->isEmployee()
        );
        $documents = array_map(fn($d) => $this->serializeDocument($app, $d), $filtered);

        return Json::encode(
            [
                'language' => $app->language,
                'documents' => $documents,
                'permissions' => $this->buildPermissionList($app, $this->client),
            ],
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );
    }
}
