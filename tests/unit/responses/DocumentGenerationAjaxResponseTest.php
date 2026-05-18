<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\responses;

use hipanel\modules\finance\responses\DocumentGenerationAjaxResponse;
use PHPUnit\Framework\TestCase;

final class DocumentGenerationAjaxResponseTest extends TestCase
{
    public function testCreatesSuccessResponseArray(): void
    {
        $entry = [
            'uuid' => 'preview-uuid',
            'url' => '/document/document/get-cached-file?uuid=preview-uuid',
            'requisite' => [
                'id' => 123,
                'name' => 'Main requisite',
            ],
        ];

        // array_values() is applied internally so string keys are stripped and data
        // is serialised as a JSON array (matching what the TypeScript client expects).
        $this->assertSame([
            'status' => 'success',
            'errors' => [],
            'message' => null,
            'data' => [$entry],
        ], DocumentGenerationAjaxResponse::success(['default' => $entry])->asArray());
    }

    public function testCreatesErrorResponseArray(): void
    {
        $this->assertSame([
            'status' => 'error',
            'errors' => ['Failed to generate document'],
            'message' => 'Failed to generate document',
            'data' => [],
        ], DocumentGenerationAjaxResponse::error('Failed to generate document')->asArray());
    }

    public function testNormalizesFlashErrors(): void
    {
        $this->assertSame([
            'status' => 'error',
            'errors' => ['First error', 'Second error'],
            'message' => 'Document generation failed',
            'data' => [],
        ], DocumentGenerationAjaxResponse::error([
            ['text' => 'First error'],
            ['text' => 'Second error'],
        ], 'Document generation failed')->asArray());
    }
}
