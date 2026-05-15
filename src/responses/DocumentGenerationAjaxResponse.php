<?php declare(strict_types=1);

namespace hipanel\modules\finance\responses;

use InvalidArgumentException;

final class DocumentGenerationAjaxResponse
{
    public const string STATUS_SUCCESS = 'success';
    public const string STATUS_ERROR = 'error';

    /**
     * @param string[] $errors
     * @param array<string, array{
     *     uuid: string,
     *     url: string,
     *     requisite: array{id: int, name: string}
     * }> $data
     */
    private function __construct(
        private readonly string $status,
        private readonly array $errors = [],
        private readonly ?string $message = null,
        private readonly array $data = [],
    )
    {
        if (!in_array($this->status, [self::STATUS_SUCCESS, self::STATUS_ERROR], true)) {
            throw new InvalidArgumentException("Unsupported ajax response status: {$this->status}");
        }
    }

    /**
     * @param array<string, array{
     *     uuid: string,
     *     url: string,
     *     requisite: array{id: int, name: string}
     * }> $data
     */
    public static function success(array $data = [], ?string $message = null): self
    {
        return new self(self::STATUS_SUCCESS, [], $message, $data);
    }

    /**
     * @param string|string[]|array<int, array{text?: string}>|null $errors
     * @param array<string, array{
     *     uuid: string,
     *     url: string,
     *     requisite: array{id: int, name: string}
     * }> $data
     */
    public static function error(string|array|null $errors = [], ?string $message = null, array $data = []): self
    {
        $normalizedErrors = self::normalizeErrors($errors);

        return new self(
            self::STATUS_ERROR,
            $normalizedErrors,
            $message ?? ($normalizedErrors[0] ?? null),
            $data,
        );
    }

    /**
     * @param string|string[]|array<int, array{text?: string}>|null $errors
     * @return string[]
     */
    private static function normalizeErrors(string|array|null $errors): array
    {
        if ($errors === null) {
            return [];
        }

        if (is_string($errors)) {
            return $errors === '' ? [] : [$errors];
        }

        $result = [];
        foreach ($errors as $error) {
            if (is_array($error) && isset($error['text']) && is_string($error['text'])) {
                $error = $error['text'];
            }

            if (is_scalar($error)) {
                $error = trim((string)$error);
                if ($error !== '') {
                    $result[] = $error;
                }
            }
        }

        return $result;
    }

    public function asArray(): array
    {
        return [
            'status' => $this->status,
            'errors' => $this->errors,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
