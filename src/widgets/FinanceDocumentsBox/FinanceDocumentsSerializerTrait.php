<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets\FinanceDocumentsBox;

use hipanel\modules\client\models\Client;
use hipanel\modules\document\models\Document;
use Yii;
use yii\web\Application;

trait FinanceDocumentsSerializerTrait
{
    public static function serializeRawDocumentEntry(array $raw): array
    {
        $parsed = json_decode($raw['data'] ?? '{}', true) ?: [];
        $raw['id'] = (string)($raw['id'] ?? '');
        $raw['file_id'] = (string)($raw['file_id'] ?? '');
        $raw['type'] = $parsed['type'] ?? $raw['type'] ?? '';
        $raw['type_label'] = Yii::$app->i18n::removeLegacyLangTags($parsed['type_label'] ?? $raw['type'] ?? '');
        $raw['date'] = explode(' ', $raw['validity_start'] ?? '', 2)[0];
        $raw['number'] = $parsed['no'] ?? $raw['id'];
        $raw['filename'] = $parsed['filename'] ?? '';
        $raw['location'] = $parsed['location'] ?? null;
        $raw['bill_id'] = $parsed['bill_id'] ?? null;

        return $raw;
    }

    private function buildPermissionList(Application $app, Client $client): array
    {
        $identityUser = $app->user;

        return array_keys(array_filter([
            'top-up' => $identityUser->can('deposit') && $this->client->isSameAsIdentity(),
            'document.read' => $identityUser->can('document.read'),
            'document.generate' => $identityUser->can('document.generate'),
            'purse.update' => $identityUser->can('purse.update'),
            'client.update' => $identityUser->can('client.update'),
            'owner-staff' => $identityUser->can('owner-staff'),
            'has-own-seller' => $identityUser->identity->hasOwnSeller($client->id),
            'is-employee' => $identityUser->can('is-employee'),
        ]));
    }

    private function filterAccessibleDocuments(Application $app, array $documents, bool $isEmployee): array
    {
        $allowedTypes = $this->resolveAccessibleDocumentTypes($app, $isEmployee);

        if ($allowedTypes === []) {
            return [];
        }

        return array_values(array_filter(
            $documents,
            static fn($document): bool => in_array((string)$document->type, $allowedTypes, true)
        ));
    }

    private function resolveAccessibleDocumentTypes(Application $app, bool $isEmployee): array
    {
        $user = $app->user;

        if (!$user->can('document.read')) {
            return [];
        }

        $documentTypes = $isEmployee
            ? ['contract', 'probation', 'nda', 'internal_invoice', 'acceptance']
            : [
                'service_invoice',
                'installment_invoice',
                'purchase_invoice',
                'old_installment_invoice',
                'old_payment_plan_payment_request',
                'service_payment_request',
                'installment_payment_request',
                'payment_plan_payment_request',
                'part_replacement_notice',
                'purchase_payment_request',
            ];

        if ($user->can('owner-staff') && !$isEmployee) {
            $documentTypes = array_merge($documentTypes, [
                'invoice',
                'detailed_service_invoice',
                'payment_request',
                'detailed_service_payment_request',
                'purchase_payment_request',
            ]);
        }

        return array_values(array_unique($documentTypes));
    }

    private function serializeDocument(Application $app, Document $document): array
    {
        $i18n = $app->i18n;
        $data = array_map(static fn($v) => $i18n::removeLegacyLangTags($v), $document->toArray());

        $data['date'] = explode(' ', $document->validity_start ?? $document->create_time ?? '', 2)[0];
        $data['number'] = $document->number ?: (string)$document->id;
        $data['location'] = $document->data_location;
        $data['bill_id'] = $document->data_bill_id;

        return $data;
    }
}
