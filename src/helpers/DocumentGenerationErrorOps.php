<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use Yii;
use yii\helpers\Html;

final class DocumentGenerationErrorOps
{
    private const array TEMPLATE_NOT_FOUND_ERRORS = [
        'failed to find the document template',
        'No templates for requisite',
    ];

    public static function buildMessage(mixed $responseData): string
    {
        $errorOps = self::extract($responseData);

        if ($errorOps === null) {
            return self::extractErrorText($responseData)
                ?? Yii::t('hipanel:finance', 'Failed to generate document');
        }

        if (Yii::$app->user->can('requisites.update')) {
            $contactUrl = Html::a(
                Yii::t('hipanel:finance', 'requisite settings'),
                ['@requisite/view', 'id' => $errorOps['requisite_id']]
            );

            return Yii::t(
                'hipanel:finance',
                "No templates for the requisite. Follow this link {contactUrl} and set a template of type '{type}'",
                ['contactUrl' => $contactUrl, 'type' => $errorOps['type']]
            );
        }

        return Yii::t('hipanel:finance', 'No templates for the requisite. Please contact the finance department');
    }

    private static function extractErrorText(mixed $responseData): ?string
    {
        if (is_string($responseData) && $responseData !== '') {
            return $responseData;
        }
        if (!is_array($responseData)) {
            return null;
        }
        $error = $responseData['_error'] ?? null;

        return is_string($error) && $error !== '' ? $error : null;
    }

    private static function extract(mixed $responseData): ?array
    {
        if (!is_array($responseData)) {
            return null;
        }

        return self::extractFromArray($responseData);
    }

    private static function extractFromArray(array $payload): ?array
    {
        $errorOps = $payload['_error_ops'] ?? null;
        if (self::isValid($errorOps) && self::isTemplateMissingError($payload)) {
            return $errorOps;
        }

        foreach ($payload as $nestedPayload) {
            if (!is_array($nestedPayload)) {
                continue;
            }

            $errorOps = self::extractFromArray($nestedPayload);
            if ($errorOps !== null) {
                return $errorOps;
            }
        }

        return null;
    }

    private static function isValid(mixed $errorOps): bool
    {
        return is_array($errorOps)
            && isset($errorOps['requisite_id'], $errorOps['type']);
    }

    private static function isTemplateMissingError(array $payload): bool
    {
        $error = $payload['_error'] ?? null;
        if (!is_string($error)) {
            return false;
        }

        return array_any(self::TEMPLATE_NOT_FOUND_ERRORS, fn($needle) => str_contains($error, $needle));
    }
}
