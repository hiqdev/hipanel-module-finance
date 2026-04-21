<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\support;

use hiqdev\hiart\RequestInterface;
use hiqdev\hiart\ResponseInterface;

final class TestHiartResponse implements ResponseInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly array $payload,
    ) {
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getData()
    {
        return $this->payload;
    }

    public function getRawData()
    {
        return json_encode($this->payload, JSON_THROW_ON_ERROR);
    }

    public function getStatusCode()
    {
        return 200;
    }

    public function getReasonPhrase()
    {
        return 'OK';
    }

    public function getHeader($name)
    {
        return null;
    }

    public function getHeaders()
    {
        return [];
    }
}
