<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\stubs;

use hipanel\modules\finance\models\decorators\server\AbstractServerResourceDecorator;
use hipanel\modules\finance\models\decorators\server\ServerResourceDecoratorFactory;

class ServerResourceStub extends AbstractResourceStub
{
    /**
     * @return AbstractServerResourceDecorator
     */
    public function decorator()
    {
        if (empty($this->decorator)) {
            $this->decorator = ServerResourceDecoratorFactory::createFromResource($this);
        }

        return $this->decorator;
    }

    public function __get($value)
    {
        return null;
    }
}
