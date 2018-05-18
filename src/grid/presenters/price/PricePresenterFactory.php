<?php

namespace hipanel\modules\finance\grid\presenters\price;

use hipanel\modules\finance\models\TemplatePrice;
use hipanel\modules\finance\models\Price;
use yii\base\InvalidConfigException;

/**
 * Class PricePresenterFactory
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PricePresenterFactory
{
    /**
     * @var array map of model class to its presenter
     */
    protected $map = [
        Price::class => PricePresenter::class,
        TemplatePrice::class => ModelGroupPricePresenter::class,
    ];

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param string $name
     * @return PricePresenter
     * @throws InvalidConfigException
     */
    public function build($name)
    {
        if (!isset($this->map[$name])) {
            throw new InvalidConfigException('Can not create presenter for class ' . $name);
        }

        if (!isset($this->cache[$name])) {
            $this->cache[$name] = new $this->map[$name]();
        }

        return $this->cache[$name];
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

}
