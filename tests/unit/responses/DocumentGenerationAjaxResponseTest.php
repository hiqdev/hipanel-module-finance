<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\responses;

use hipanel\modules\finance\responses\DocumentGenerationAjaxResponse;
use PHPUnit\Framework\TestCase;

final class DocumentGenerationAjaxResponseTest extends TestCase
{
    public function testCreatesSuccessResponseArray(): void
    {
        $data = [
            'default' => [
                'uuid' => 'preview-uuid',
                'url' => '/document/document/get-cached-file?uuid=preview-uuid',
                'requisite' => [
                    'id' => 123,
                    'name' => 'Main requisite',
                ],
            ],
        ];

        $this->assertSame([
            'status' => 'success',
            'errors' => [],
            'message' => null,
            'data' => $data,
        ], DocumentGenerationAjaxResponse::success($data)->asArray());
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
