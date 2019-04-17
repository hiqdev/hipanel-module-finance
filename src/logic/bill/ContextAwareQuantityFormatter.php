<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic\bill;

/**
 * Interface ContextAwareQuantityFormatter should be used to mark formatter context-aware,
 * so it can use information related to the quantity.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface ContextAwareQuantityFormatter
{
    /**
     * @param mixed $context
     * @return ContextAwareQuantityFormatter
     */
    public function setContext($context): self;
}
