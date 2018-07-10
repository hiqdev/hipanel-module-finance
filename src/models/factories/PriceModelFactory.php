<?php

namespace hipanel\modules\finance\models\factories;

use hipanel\modules\finance\models\TemplatePrice;
use hipanel\modules\finance\models\Price;
use yii\base\InvalidConfigException;

/**
 * Class PriceModelFactory builds objects, that must be children of [[Price]] class
 * using short class name. See definitions in $map property.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceModelFactory
{
    /**
     * @var array
     */
    protected $map = [
        'Price' => Price::class,
        'TemplatePrice' => TemplatePrice::class,
        'SinglePrice' => Price::class,
    ];

    /**
     * @param string $name
     * @return Price
     * @throws InvalidConfigException
     */
    public function build($name)
    {
        if (!isset($this->map[$name])) {
            throw new InvalidConfigException('Can not create model for class ' . $name);
        }

        return new $this->map[$name]();
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }
}
