<?php

namespace hipanel\modules\finance\models\decorators;


abstract class AbstractResourceDecorator implements ResourceDecoratorInterface
{
    /**
     * @var Resource
     */
    protected $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }
}
