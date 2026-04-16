<?php
declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

final class DocumentGenerationErrorOps
{
    /**
     * Extracts _error_ops from a HiArt response payload.
     *
     * Supports both flat responses (root-level _error_ops) and batch responses
     * where _error_ops is nested inside a per-model entry.
     */
    public static function extract(mixed $responseData): ?array
    {
        if (!is_array($responseData)) {
            return null;
        }

        $errorOps = $responseData['_error_ops'] ?? null;
        if (self::isValid($errorOps)) {
            return $errorOps;
        }

        foreach ($responseData as $payload) {
            if (!is_array($payload)) {
                continue;
            }

            $errorOps = $payload['_error_ops'] ?? null;
            if (self::isValid($errorOps)) {
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
}
