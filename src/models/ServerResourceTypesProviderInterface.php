<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

/**
 * Interface ServerResourceTypesProviderInterface.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface ServerResourceTypesProviderInterface
{
    /**
     * Returns array of supported server resource types.
     *
     * Format:
     *  - key (string): the type name, e.g. `server`
     *  - value (string): the type label, e.g. `Dedicated server`
     *
     * @return array
     */
    public function getTypes();
}
