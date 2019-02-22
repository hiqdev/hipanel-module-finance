<?php

namespace hipanel\modules\finance\grid\presenters\price;

use hipanel\modules\finance\models\CertificatePrice;
use hipanel\modules\finance\models\DomainServicePrice;
use hipanel\modules\finance\models\DomainZonePrice;
use hipanel\modules\finance\models\TemplatePrice;
use hipanel\modules\finance\models\Price;
use Psr\Container\ContainerInterface;
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
        TemplatePrice::class => TemplatePricePresenter::class,
        CertificatePrice::class => CertificatePricePresenter::class,
        '*' => PricePresenter::class,
    ];

    /**
     * @var array
     */
    protected $cache = [];
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @return PricePresenter
     * @throws InvalidConfigException
     */
    public function build($name)
    {
        $className = $this->map[$name] ?? $this->map['*'];

        if (!isset($this->cache[$name])) {
            $this->cache[$name] = $this->container->get($className);
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
