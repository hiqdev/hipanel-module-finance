<?php

namespace hipanel\modules\finance\models;

/**
 * Interface ServerResourceTypesProviderInterface
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
