<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

final class BatchPerformHelper
{
    /**
     * Merges all sub-arrays from a batchPerform response into a single flat list.
     * batchPerform returns [[results_for_payload_0], [results_for_payload_1], ...].
     * This method flattens one level regardless of how many payloads were sent.
     */
    public static function unwrapResults(mixed $batchResponse): array
    {
        return array_merge(
            ...array_map(
                static fn(mixed $sub): array => array_values((array)$sub),
                array_values((array)$batchResponse),
            )
        );
    }
}
