<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\support;

use hiqdev\hiart\RequestInterface;

final class TestRequest implements RequestInterface
{
    public function getDbname()
    {
        return null;
    }

    public function getMethod()
    {
        return 'POST';
    }

    public function getUri()
    {
        return null;
    }

    public function getFullUri()
    {
        return 'http://localhost/purse/generate-and-save-monthly-document';
    }

    public function getHeaders()
    {
        return [];
    }

    public function getBody()
    {
        return '{}';
    }

    public function getVersion()
    {
        return '1.1';
    }

    public function getQuery()
    {
        return null;
    }

    public function build()
    {
    }

    public function send($options = [])
    {
        return null;
    }

    public static function isSupported()
    {
        return true;
    }

    public function serialize(): string
    {
        return '';
    }

    public function unserialize(string $serialized): void
    {
    }

    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {
    }
}
