<?php
declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

final class DocumentGenerationErrorOps
{
    private const TEMPLATE_NOT_FOUND_ERRORS = [
        'failed find document template',
        'No templates for requisite',
    ];

    /**
     * Extracts _error_ops from a HiArt response payload.
     *
     * Supports flat responses and nested batch responses where template-related
     * _error_ops can be placed multiple levels deep inside per-model entries.
     */
    public static function extract(mixed $responseData): ?array
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

        foreach (self::TEMPLATE_NOT_FOUND_ERRORS as $needle) {
            if (str_contains($error, $needle)) {
                return true;
            }
        }

        return false;
    }
}
